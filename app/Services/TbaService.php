<?php

namespace App\Services;

use App\Enums\Video\Encoder;
use App\Factories\Src\SrcServiceFactory;
use App\Helpers\CoreService\CoreServiceApi;
use App\Helpers\Custom\GlobalPlatform;
use App\Helpers\Path\Tba;
use App\Http\Resources\TbaCommentObsrvCollection;
use App\Libraries\Azure\Blob;
use App\Notifications\EventChannel;
use App\Repositories\ResourceRepository;
use App\Repositories\TbaAnalysisEventRepository;
use App\Repositories\TbaAnnexRepository;
use App\Repositories\TbaRepository;
use App\Repositories\TbaStatisticRepository;
use App\Types\Device\DeviceType;
use App\Types\Src\SrcType;
use App\Types\Src\VodType;
use App\Types\Tba\AnnexType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\File;
use Illuminate\Support\Collection;
use ZipArchive;

/**
 *
 */
class TbaService extends BaseService
{
    use Tba, CoreServiceApi;

    /**
     * @var TbaRepository
     */
    protected $repository;
    /**
     * @var ResourceRepository
     */
    protected $resourceRepository;
    /**
     * @var TbaAnalysisEventRepository
     */
    protected $analysisEventRepository;
    /**
     * @var TbaStatisticRepository
     */
    protected $tbaStatisticRepository;

    /**
     * @var TbaAnnexRepository
     */
    protected $tbaAnnexRepository;
    /**
     * @var SrcServiceFactory
     */
    protected $srcServiceFactory;
    /**
     * @var TbaCommentService
     */
    protected $tbaCommentService;
    /**
     * @var TagService
     */
    protected $tagService;
    /**
     * @var UserService
     */
    protected $userService;
    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * TbaService constructor.
     * @param TbaRepository $repository
     * @param ResourceRepository $resourceRepository
     * @param TbaAnalysisEventRepository $analysisEventRepository
     * @param TbaStatisticRepository $tbaStatisticRepository
     * @param TbaCommentService $tbaCommentService
     * @param TbaAnnexRepository $tbaAnnexRepository
     * @param SrcServiceFactory $srcServiceFactory
     * @param TagService $tagService
     * @param UserService $userService
     * @param GroupService $groupService
     */
    public function __construct(
        TbaRepository              $repository,
        ResourceRepository         $resourceRepository,
        TbaAnalysisEventRepository $analysisEventRepository,
        TbaStatisticRepository     $tbaStatisticRepository,
        TbaCommentService          $tbaCommentService,
        TbaAnnexRepository         $tbaAnnexRepository,
        SrcServiceFactory          $srcServiceFactory,
        TagService                 $tagService,
        UserService                $userService,
        GroupService               $groupService
    )
    {
        $this->repository              = $repository;
        $this->resourceRepository      = $resourceRepository;
        $this->analysisEventRepository = $analysisEventRepository;
        $this->tbaStatisticRepository  = $tbaStatisticRepository;
        $this->tbaCommentService       = $tbaCommentService;
        $this->tbaAnnexRepository      = $tbaAnnexRepository;
        $this->srcServiceFactory       = $srcServiceFactory;
        $this->tagService              = $tagService;
        $this->userService             = $userService;
        $this->groupService            = $groupService;
    }

    /**
     * 新增 頻道課例
     * @param $tba_id
     * @param $channel_id
     * @param $attributes
     * @return bool|void
     */
    public function createGroupChannelContent($tba_id, $channel_id, $attributes)
    {
        return $this->repository->createGroupChannelContent($tba_id, $channel_id, $attributes);
    }

    /**
     * 刪除頻道課例
     * @param int $tba_id
     * @param int $channel_id
     * @return false|int
     */
    public function deleteGroupChannelContent(int $tba_id, int $channel_id)
    {
        return $this->repository->deleteGroupChannelContent($tba_id, $channel_id);
    }

    /**
     * 取tba 相關 統計數據
     * @param $channel_id
     * @return \App\Models\Tba[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection
     */
    public function getTbaStatsInfo($channel_id)
    {
        return $this->repository->getTbaStats($channel_id);
    }


    /**
     * 取tba 相關 統計數據 韓篩選功能
     * @param int $channel_id
     * @param null $date
     * @param null $search
     * @param null $column
     * @return \App\Models\Tba[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection
     */
    public function getTbaStatsInfoAndFilter(int $channel_id, $date = null, $search = null, $column = null)
    {
        switch ($column) {
            case 'name':
                $column = 'users.name';
                break;
            case 'habook':
                $column = 'tbas.habook_id';
                break;
            case 'subject':
                $column = 'group_channel_contents.group_subject_fields_id';
                break;
            case 'grade':
                $column = 'group_channel_contents.grades_id';
                break;
            case 'rating':
                $column = 'group_channel_contents.ratings_id';
                break;
            default:
                $column = null;
        }

        return $this->repository->getTbaStatsOrFilter($channel_id, $date, $search, $column);
    }

    /**
     * 上傳傳統影片
     * @param int $user_id
     * @param array $tbaData
     * @param array $resourceData
     * @param string $fileName
     * @param object $file
     * @param array $groupChannelDate
     * @return \App\Models\Tba|false|Builder|\Illuminate\Database\Eloquent\Model
     */
    public function uploadVideo(int $user_id, array $tbaData, array $resourceData, string $fileName, object $file, array $groupChannelDate)
    {

        try {
            $duration     = GlobalPlatform::getDuration($file);
            $tbaInfo      = $this->repository->createTba($user_id, $tbaData);
            $resourceInfo = $this->resourceRepository->createResrc($user_id, $resourceData);

            $rid = $tbaInfo->id . '/' . $tbaInfo->id . '.mp4'; //ex 9000/9000.mp4 (assume it's mp4)

            $rdata = [
                'source'    => getenv('BLOB_SOURCE'),
                'blob'      => $rid, //ex $rid
                'container' => getenv('BLOB_VIDEO_CONTAINER'),
                'file_size' => $file->getSize(), //ex 影片大小
                'duration'  => $duration, //ex 影片長度
            ];
            // Create Vod
            $resourceInfo->vod()->create([
                'type'    => VodType::AzureFile,
                'rid'     => $rid,
                'rstatus' => 'Normal',
                'rdata'   => json_encode($rdata),
            ]);

            // Create Videos
            $video = $tbaInfo->videos()->create([
                'user_id'        => $user_id,
                'resource_id'    => $resourceInfo->id,
                'name'           => $fileName,
                'description'    => null,
                'encoder'        => 'FileUpload',
                'tbavideo_order' => 1
            ]);

            // Update tba_video
            $tbaInfo->videos()->first()->pivot->update(['tbavideo_order' => 1]);

            // create tbaStatistics
            $tbaInfo->tbaStatistics()->create([
                'type' => 47,
                'idx'  => 0,
            ]);
            $tbaInfo->tbaStatistics()->create([
                'type' => 48,
                'idx'  => 0,
            ]);
            // Create tbaVideoMaps
            if (isset($video->id)) {
                $tbaInfo->tbaVideoMaps()->create([
                    'video_id'  => $video->id,
                    'tba_start' => 0,
                    'tba_end'   => $duration
                ]);
            }


            $channelId = GlobalPlatform::convertGroupIdToChannelId($groupChannelDate['group_id']);
            $blob      = new Blob(getenv('BLOB_ACCOUNT'), getenv('BLOB_KEY'), getenv('ENDPOINT'));
            $blob->update($rid, getenv('BLOB_VIDEO_CONTAINER'), $file);
            $tbaInfo->groupChannels()->attach($channelId, $groupChannelDate);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return false;
        }
        return $tbaInfo;
    }

    /**
     * 建立 Tba 相關資料
     * @param int $user_id
     * @param string $sourceImage
     * @param array $tba
     * @param array $anal
     * @param array $eval
     * @param array $stat
     * @param array $annex
     * @param array $comments
     * @param string $file_path
     * @param $blobUri
     * @param $blobSass
     * @param string $deviceType
     * @return \App\Models\Tba|Builder|\Illuminate\Database\Eloquent\Model
     */
    public function createTba(
        int    $user_id,
        string $sourceImage,
        array  $tba,
        array  $anal,
        array  $eval,
        array  $stat,
        array  $annex,
        array  $comments,
        string $file_path,
        string $blobUri,
        string $blobSass,
        string $deviceType = DeviceType::HiTeach
    )
    {
        $tba  = $this->repository->createTba($user_id, $tba);
        $file = new Filesystem();
        // Create Directory
        if (!$file->exists($tbaFolder = $this->pathPublicTba($tba->id, null, true))) {
            $file->makeDirectory($tbaFolder);
        }
        // Create report image
        if ($file->exists($event = $file_path . '/SokratesResults/event.png')) {
            if (!$file->exists($tbaFolder . 'report.png'))
                $file->copy($event, $tbaFolder . 'report.png');
        }
        if (isset($tba->thumbnail)) {    // Create tba image
            if ($file->exists($sourceImagePath = $file_path . $sourceImage)) {
                $file->copy($sourceImagePath, $tbaFolder . $tba->thumbnail);
            }
        }

        if (!empty($anal)) {
            $this->analysisEventRepository->createEventGroups($tba->id, $tba->observation_offset, $anal['events']);
        }

//        if (isset($eval['eventFile']) && !is_null($eval['eventFile'])) {
//
//            $this->uploadCompressedFile($tba->id, $eval['eventFile']);
//        }

        $this->tbaCommentService->deleteWhere(['tba_id' => $tba->id, 'comment_type' => 1]);
        if (!empty($eval)) {
            $this->tbaCommentService->createUserCommentGroup($tba->id, $eval['users']);
        }


        if (!empty($comments)) {
            foreach ($comments as $comment) {
                $comment['tba_id'] = $tba->id;
                // 判斷標籤存不存在
                if ($this->tagService->exists(['id' => $comment['tag_id']])) {
                    $commentModel = $this->tbaCommentService->create($comment);
                    $this->tbaCommentService->convertTbaCommentToTbaEvaluateEvent($commentModel->id);
                    $url = null;
                    if (!is_null($comment['attachment'])) {
                        if ($deviceType === DeviceType::HiTeach) {
                            $url = $blobUri . '/Sokrates/' . $comment['habook_id'] . '/' . $comment['attachment'] . '?' . $blobSass;
                        }
                        if ($deviceType === DeviceType::App) {
                            $url = $blobUri . $comment['habook_id'] . '/' . $comment['attachment'] . '?' . $blobSass;
                        }

                        if (isset($commentModel->id)) {
                            $file_data = [
                                'tbaId'     => $comment['tba_id'],
                                'commentId' => $commentModel->id,
                                'name'      => basename($comment['attachment'], '.' . pathinfo($comment['attachment'], PATHINFO_EXTENSION)), // file name is timestamp
                                'ext'       => pathinfo($comment['attachment'], PATHINFO_EXTENSION),
                                'size'      => filesize($file_path),
                                'path'      => $url,
                            ];
                            $this->tbaCommentService->createCommentAttachment($commentModel, $file_data);
                        }
                    }
                }
            }
        }
//        if (isset($stat['img']) && !is_null($stat['img'])) {
//
//            $this->uploadStatImg($tba->id, $stat['img']);
//        }
        if (!empty($stat)) {
            $this->tbaStatisticRepository->createStats($tba->id, $stat['list']);
        }


        if (!empty($annex)) {
            $annex['file'] = (isset($annex['file']) && !is_null($annex['file'])) ? $annex['file'] : null;
            $this->createTbaAnnexes($user_id, $tba->id, $annex['list'], $annex['file']);
        }

        return $tba;
    }

    /**
     * 建立 Video 相關資料
     * @param int $user_id
     * @param int $channel_id
     * @param \App\Models\Tba $tba
     * @param array $resourceData
     * @param array $groupChannelData
     * @param Collection $info
     * @param string $sourceURL
     * @param int $content_status
     * @param bool $onlyVideo
     * @return bool
     */
    public function createVideo(int $user_id, int $channel_id, \App\Models\Tba $tba, array $resourceData, array $groupChannelData, Collection $info, string $sourceURL, int $content_status, bool $onlyVideo = false): bool
    {
        $Successful   = true;
        $recordFile   = $info->get('record_file');
        $rid          = $recordFile ? $tba->id . '/' . $recordFile : 'default/video.mp4'; //ex 9000/9000.mp4 (assume it's mp4)
        $resourceInfo = $this->resourceRepository->createResrc($user_id, $resourceData);

        $rdata = [
            'source'    => getenv('BLOB_SOURCE'),
            'blob'      => $rid, //ex $rid
            'container' => getenv('BLOB_VIDEO_CONTAINER'),
            'file_size' => $recordFile ? $info->get('record_filesize') : 0, //ex 影片大小
            'duration'  => $recordFile ? $info->get('record_duration') : 5, //ex 影片長度
        ];

        try {
            // Create Vod
            $resourceInfo->vod()->updateOrCreate([
                'resource_id' => $resourceInfo->id,
            ], [
                'type'    => VodType::AzureFile,
                'rstatus' => 'Normal',
                'rdata'   => json_encode($rdata),
            ]);

            // Create Videos
            $video = $tba->videos()->updateOrCreate([
                'user_id'     => $user_id,
                'resource_id' => $resourceInfo->id,
            ],
                [
                    'user_id'     => $user_id,
                    'resource_id' => $resourceInfo->id,
                    'name'        => $info->get('name'),
                    'description' => $info->get('description'),
                    'encoder'     => $info->get('encoder') ?? Encoder::FILE_UPLOAD,
                    'thumbnail'   => $tba->thumbnail,
                ]
            );
            // Update tba_video
            $tba->videos()->first()->pivot->update(['tbavideo_order' => 1]);
            // Create tbaVideoMaps
            $tba->tbaVideoMaps()->updateOrCreate([
                'video_id' => $video->id,
            ], [
                'tba_start' => $info->get('start') ?? 0,
                'tba_end'   => $info->has('end') ? $info->get('end') : $info->get('record_duration') ?? 5,
            ]);

            $blob    = new Blob(getenv('BLOB_ACCOUNT'), getenv('BLOB_KEY'), getenv('ENDPOINT'));
            $crashed = 1;// 複製影片 retry 3次 等待5
            do {
                if (empty($recordFile)) {
                    $tba->groupChannels()->detach($channel_id);
                    $tba->groupChannels()->attach($channel_id, $groupChannelData);
                    if ($content_status != 4 && !$onlyVideo) {
                        \Log::info('notify', ['status' => $this->sendNotifyForCustom($tba->id, $channel_id)]);
                    }
                    break;
                }
                if ($status = strpos(get_headers($sourceURL)[0], '200')) {
                    $blob->deleteBlob($rid, getenv('BLOB_VIDEO_CONTAINER'));
                    $blob->copyVideo(getenv('BLOB_VIDEO_CONTAINER'), $rid, $sourceURL);
                    $tba->groupChannels()->detach($channel_id);
                    $tba->groupChannels()->attach($channel_id, $groupChannelData);
                    if ($content_status != 4 && !$onlyVideo) {
                        \Log::info('notify', ['status' => $this->sendNotifyForCustom($tba->id, $channel_id)]);
                    }
                    break;
                } else {
                    \Log::info('Blob Video',
                        [
                            'end'                => Carbon::now()->format('Ymdhis'),
                            'container'          => getenv('BLOB_VIDEO_CONTAINER'),
                            'rid'                => $rid,
                            'source'             => $sourceURL,
                            'Blob_Source_Status' => $status
                        ]);
                    sleep(5);
                    $crashed++;
                }
            } while ($crashed <= 3);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            $Successful = false;
        }

        return $Successful;
    }

    /**
     * @param $userId
     * @param $tbaId
     * @param $annexes
     * @param null $annexFile
     */
    public function createTbaAnnexes($userId, $tbaId, &$annexes, $annexFile = null)
    {
        if (!is_null($annexFile)) {
            \Storage::putFileAs($this->pathTba($tbaId, 'tmp'), $file = new File($annexFile), $file->getFilename());
        }

        $tmpPath = $this->pathTba($tbaId, 'tmp');
        foreach ($annexes as $annex) {
            $annex         = collect($annex);
            $annex['name'] = $tbaId;
            $srcType       = AnnexType::getSrcType($annex->get('type'));
            if (!$srcType) {
                continue;
            }

            if ($srcType === SrcType::File) {
                $annex['file'] = $tmpPath . $annex['path'];
            }

            $resrc  = ['src_type' => $srcType, 'name' => $annex->get('name')];
            $resrc  = $this->resourceRepository->createResrc($userId, $resrc);
            $srcSrv = $this->srcServiceFactory->create($resrc->src_type);
            $src    = $srcSrv->createSrc($resrc->id, $annex);
            $this->resourceRepository->setResrc($resrc->id, ['status' => 1]);
            $annex = ['resource_id' => $resrc->id, 'type' => $annex->get('type')];
            $this->tbaAnnexRepository->createAnnex($tbaId, $annex);
        }
    }

    /**
     * @param $tbaId
     * @param $file
     */
    private function uploadCompressedFile($tbaId, $file)
    {
        $tarPath = $this->pathTba($tbaId, 'tmp', true);
        $zipper  = new ZipArchive;
        if ($zipper->open($file->path()) === TRUE) {
            $zipper->extractTo($tarPath);
            $zipper->close();
        }
    }

    //

    /**
     * @param $tbaId
     * @param $file
     */
    private function uploadStatImg($tbaId, $file)
    {
        $tarPath = $this->pathPublicTba($tbaId, null, true);
        $zipper  = new ZipArchive;
        if ($zipper->open($file->path()) === TRUE) {
            $zipper->extractTo($tarPath);
            $zipper->close();
        }
    }

    /**
     * @param int $tba_id
     * @param int $channel_id
     * @return bool
     */
    public function sendNotifyForCustom(int $tba_id, int $channel_id): bool
    {
        $isSuccessFul = true;
        try {
            $group_id  = GlobalPlatform::convertChannelIdToGroupId($channel_id);
            $groupInfo = $this->groupService->find($group_id);

            switch ($groupInfo->country_code) {
                case 886:
                    \App::setLocale('tw');
                    break;
                case 1:
                    \App::setLocale('en');
                    break;
                default:
                    \App::setLocale('cn');
            }
            // Create Message
            $tbaInfo     = $this->getObsrvTbaInfo($channel_id, $tba_id)->first();
            $commentInfo = collect(new TbaCommentObsrvCollection($this->tbaCommentService->getComments($tba_id, 1)));
            $message     = [
                'channel_id'  => $channel_id,
                'title'       => null,
                'content'     => null,
                'url'         => null,
                'isOperating' => false,
                'top'         => false,
            ];

            $message['title'] = __('video-upload-message.title', [
                'lecture_date' => $tbaInfo->lecture_date,
                'grade'        => $tbaInfo->grade,
                'subject'      => $tbaInfo->subject,
                'name'         => $tbaInfo->name,
                'teacher'      => $tbaInfo->teacher
            ]);


            $irsAvg             = !empty($tbaInfo->student_count) ? number_format(($tbaInfo->irs_count / $tbaInfo->student_count), 1) : 0;
            $observerCount      = count($commentInfo['observers']);
            $commentCount       = count($commentInfo['observerComments']);
            $message['content'] = __('video-upload-message.content', [
                'group_name'    => $groupInfo->name,
                'name'          => $tbaInfo->name,
                'teacher'       => $tbaInfo->teacher,
                'user'          => $tbaInfo->user,
                'subject'       => $tbaInfo->subject,
                'grade'         => $tbaInfo->grade,
                'student_count' => $tbaInfo->student_count,
                'irs_count'     => $tbaInfo->irs_count,
                'lecture_date'  => $tbaInfo->lecture_date,
                'observerCount' => $observerCount,
                'commentCount'  => $commentCount,
                'habook'        => $tbaInfo->user->habook,
                'irsAvg'        => $irsAvg
            ]);
            $message['url']     = url(getenv('SOKRADEO_URL') . "/exhibition/tbavideo#/content/$tba_id?groupIds=$group_id&channelId=$channel_id");
            // For app notify
            $hiTeachMessage = json_encode([
                'content' => $message['content'],
                'action'  => [
                    [
                        'type'          => 'click',
                        'label'         => __('video-upload-message.click'),
                        'url'           => getenv('SOKRADEO_URL') . '/exhibition/tbavideo/check-with-habook/?to=' . base64_encode($message['url']) . '&ticket=',
                        'tokenbindtype' => 1
                    ]
                ]
            ]);
            // teamModel ID
            $haBook  = array_map(function ($item) {
                return $item['habook'];
            }, $commentInfo['observers']);
            $haBooks = collect(array_merge($haBook, $groupInfo->users->pluck('habook')->toArray()))->unique()->values()->all();

            if ((int)$groupInfo->public === 0 && (int)$groupInfo->review_status === 0) {
                $this->userService->findWhereIn('habook', $haBooks)->each(function ($user) use ($message) {
                    $user->notify(new EventChannel($message));
                });
                // send app notify
                \Log::info('App Notify', ['status' => $this->sendNotify($haBooks, $hiTeachMessage, $message['title'], $groupInfo->notify_status)]);
            }
        } catch (\Exception $exception) {
            \Log::debug('notify', [$exception->getMessage()]);
            $isSuccessFul = false;
        }

        return $isSuccessFul;
    }

    /**
     * TbaInfo for Observation
     * @param int $groupChannelId
     * @param int $contentId
     * @return \App\Models\Tba[]|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getObsrvTbaInfo(int $groupChannelId, int $contentId)
    {
        return $this->repository->getTbaInfo($groupChannelId, $contentId);
    }
}
