<?php

namespace App\Repositories;

use App\Helpers\Path\Tba as TbaPath;
use App\Models\Resource;
use App\Models\Uri;
use Illuminate\Support\Facades\DB;

class UriRepository extends BaseRepository
{
    use TbaPath;

    protected $model;

    //
    public function __construct(Uri $model)
    {
        $this->model = $model;
    }

    //
    public function list($page = 1)
    {
        return $this->model->query()->paginate(null, ['*'], 'page', $page);
    }

    //
    public function listByUserId($userId, $page = 1)
    {
        // 待實作
    }

    //
    public function getUri($uriId)
    {
        return $this->model->query()->findOrFail($uriId);
    }

    //
    public function getUriByResourceId($resourceId)
    {
        $tba                  = DB::table('resources')
            ->join('videos', 'resources.id', 'videos.resource_id')
            ->join('tba_video', 'videos.id', 'tba_video.video_id')
            ->where('resources.id', $resourceId)
            ->get();
        $data                 = Resource::with(['uri'])->where('id', $resourceId)->firstOrFail();
        $data->uri->thumbnail = '//' . $_SERVER['SERVER_NAME'] . '/storage/tba/' . $tba[0]->tba_id . '/' . $tba[0]->thumbnail;
        return $data;
    }

    //
    public function createUri($resrcId, $uri)
    {
        /*
        if ($this->>model->query()->where('resource_id', $resrcId)->exists()) {
            throw new LogicException('resrc of uri is already exist');
        }
        */
        $uri['resource_id'] = $resrcId;
        return $this->model->query()->create($uri);
    }

}
