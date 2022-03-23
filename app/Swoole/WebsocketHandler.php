<?php

namespace App\Swoole;

use App\Libraries\Azure\Blob;
use App\Models\Tag;
use App\Models\TagType;
use App\Services\App\ObservationService;
use App\Services\ObservationClassService;
use App\Services\TbaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use SwooleTW\Http\Server\Manager;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Swoole\Websocket\Frame;
use SwooleTW\Http\Server\Facades\Server;
use SwooleTW\Http\Websocket\HandlerContract;
use SwooleTW\Http\Server\Facades\Server as ClientServer;

class WebsocketHandler implements HandlerContract
{

    // action method
    const START = 'START';
    const ET = 'ET';
    const END = 'END';
    private $server;
    /**
     * @var ObservationClassService
     */
    protected $observationClassService;
    /**
     * @var ObservationService
     */
    protected $observationService;
    /**
     * @var TbaService
     */
    protected $tbaService;

    public function __construct(ObservationClassService $observationClassService, ObservationService $observationService, TbaService $tbaService)
    {
        /** @var Manager $manager */
        $this->server                  = App::make(ClientServer::class);
        $this->observationClassService = $observationClassService;
        $this->observationService      = $observationService;
        $this->tbaService              = $tbaService;
    }

    /**
     * "onOpen" listener.
     * @param int $fd
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function onOpen($fd, Request $request): bool
    {
        /**
         * 客户端建立起长链接后，返回客户端fd
         */
        $this->server->push($fd, json_encode(['event' => 'open', 'data' => ['fd' => $fd]]));
        return true;
    }


    /**
     * "onMessage" listener.
     *  only triggered when event handler not found
     *  Action  START/ET/END
     * @param \Swoole\Websocket\Frame $frame
     * @return bool
     */
    public function onMessage(Frame $frame): bool
    {
        //{ id:xx,method:post,active:xxx,binding_number }
        $result = json_decode($frame->data);
        switch (\Str::lower($result->method)) {
            case 'post':
                $this->createJson($result->id);
                break;
            case 'get':
                $this->getTime($frame->fd, $result->id);
                break;
            case 'put':
                if (\Str::upper($result->action) === self::END) {
                    $model = $this->observationService->createLesson($result->id);
                    $this->server->push($frame->fd, json_encode(['event' => 'get', 'tba' => $model]));
                }
                $this->createJson($result->id);
                break;
            default:
                Log::info('Websocket', ['message' => 'No such method', 'status' => 0]);
                break;
        }
        return true;
    }

    /**
     * "onClose" listener.
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose($fd, $reactorId)
    {
        echo 'close' . PHP_EOL;
        return;
    }

    /**
     * 建立json file
     * @param $id
     */
    private function createJson($id)
    {
        $container  = getenv('BLOB_VIDEO_CONTAINER');
        $blobServer = new Blob(getenv('BLOB_ACCOUNT'), getenv('BLOB_KEY'), getenv('ENDPOINT'));
        try {
            $observation = $this->observationClassService->firstWhere(['id' => $id]);
            $blob        = 'observation/' . $observation->binding_number . '/observation.json';
            $jsonData    = json_encode([
                'className'       => $observation->name,
                'classTeacher'    => $observation->teacher,
                'classTeacher_id' => $observation->habook_id,
                'duration'        => $observation->duration,
                'status'          => $observation->status,
                'timestamp'       => $observation->timestamp ? Carbon::parse($observation->timestamp)->timestamp : null,
            ]);
            $blobServer->update($blob, $container, $jsonData, false);
            $school = [];
            $tmd    = [];
            // School TagType
            $school['tableType'] = 'school';
            $school['types']     = TagType::query()->where('status', 1)->where('group_id', $observation->group_id)->orderBy('order')->get()->map(function ($tagType) {
                $tags = Tag::query()->where('status', 1)->where('type_id', $tagType->id)->select('id', 'content')->orderBy('id')->get()->map(function ($tag) {
                    $tagContents = collect(json_decode($tag->content, true));
                    $tagContents->put('id', $tag->id);
                    return $tagContents->toArray();
                })->toArray();
                return [
                    'typeId'   => $tagType->id,
                    'order'    => $tagType->order,
                    'typeName' => json_decode($tagType->content, true),
                    'tagList'  => $tags,
                ];
            })->toArray();
            // Tmd TagType
            $tmd['tableType'] = 'tmd';
            $tmd['types']     = TagType::query()->where('status', 1)->whereNull('group_id')->whereNull('user_id')->orderBy('order', 'ASC')->get()->map(function ($tagType) {
                $tags = Tag::query()->where('status', 1)->where('type_id', $tagType->id)->select('id', 'content')->orderBy('id')->get()->map(function ($tag) {
                    $tagContents = collect(json_decode($tag->content, true));
                    $tagContents->put('id', $tag->id);
                    return $tagContents->toArray();
                })->toArray();
                return [
                    'typeId'   => $tagType->id,
                    'order'    => $tagType->order,
                    'typeName' => json_decode($tagType->content, true),
                    'tagList'  => $tags,

                ];
            })->toArray();

            $tags    = json_encode([
                $school,
                $tmd,
            ]);
            $tagBlob = 'observation/' . $observation->binding_number . '/observation_tags.json';
            $blobServer->update($tagBlob, $container, $tags, false);
            Log::info('observation', ['Binding_number' => $observation->binding_number, 'status' => false]);
        } catch (\Exception $exception) {
            Log::info('observation', [$exception->getMessage(), 'status' => false]);
        }
    }

    /**
     * 使用者時間
     * @param $fd
     * @param $id
     */
    protected function getTime($fd, $id)
    {
        $observation = $this->observationClassService->firstWhere(['id' => $id]);
        $countUsers  = $observation->observationUsers()->count();
        $status      = $observation->status !== 'E';
        $this->server->push($fd, json_encode(['event' => 'get', 'status' => $status, 'user_total' => $countUsers, 'time' => Carbon::now()->timestamp]));
    }
}
