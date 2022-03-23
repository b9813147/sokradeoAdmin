<?php

namespace App\Http\Controllers\Api\V1\App\Video;

use App\Helpers\CoreService\CoreServiceApi;
use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\V1\Controller;
use App\Services\GroupService;
use App\Services\GroupSubjectFieldsService;
use App\Services\RatingService;
use App\Services\TagTypeService;
use App\Services\TbaService;
use App\Services\UserService;
use App\Types\Src\SrcType;
use App\Types\Tba\AnnexType;
use App\Types\Tba\StatisticType;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use ZipArchive;

class LessonController extends Controller
{
    use CoreServiceApi;

    protected $userService;
    protected $tbaService;
    /**
     * @var RatingService
     */
    protected $ratingService;
    /**
     * @var GroupService
     */
    protected $groupService;
    /**
     * @var GroupSubjectFieldsService
     */
    protected $groupSubjectFieldsService;
    /**
     * @var TagTypeService
     */
    protected $tagTypeService;

    /**
     * @param TbaService $tbaService
     * @param UserService $userService
     * @param RatingService $ratingService
     * @param GroupService $groupService
     * @param GroupSubjectFieldsService $groupSubjectFieldsService
     * @param TagTypeService $tagTypeService
     */
    public function __construct(
        TbaService                $tbaService,
        UserService               $userService,
        RatingService             $ratingService,
        GroupService              $groupService,
        GroupSubjectFieldsService $groupSubjectFieldsService,
        TagTypeService            $tagTypeService
    )
    {
        $this->tbaService                = $tbaService;
        $this->userService               = $userService;
        $this->groupService              = $groupService;
        $this->ratingService             = $ratingService;
        $this->groupSubjectFieldsService = $groupSubjectFieldsService;
        $this->tagTypeService            = $tagTypeService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'abbr'      => 'string',
            'blobUri'   => 'required|string',
            'zipFile'   => 'required|file',
            'onlyVideo' => 'bool'
        ]);
        $url        = null;
        $fileSystem = new Filesystem();
        $zipper     = new ZipArchive;
        $abbr       = $request->exists('abbr') ? $request->input('abbr') : '';
        $blobUri    = $request->input('blobUri');
        $onlyVideo  = $request->input('onlyVideo') ?? false;
        $zipFile    = $request->zipFile->store('public');
        $blobSas    = $this->getBlobSas($request->bearerToken(), $abbr);
        $record_id  = explode('/', $blobUri)[5];
        $tempZip    = storage_path("app/$zipFile");// 壓縮暫存位置
        $temp       = storage_path('temp/lesson/' . $record_id); // 解壓縮暫存位置
        $file_path  = $temp . '/Sokrates/'; // 檔案位置

        try {
            // Unzip
            if ($zipper->open($tempZip) === TRUE) {
                $zipper->extractTo($temp);
                $fileSystem->delete($tempZip);
                $zipper->close();
            }


            if (!$fileSystem->exists($file_path)) {
                return $this->setStatus(404)->fail(['message' => 'File does not exist']);
            }

            $info = $this->jsonTransform($record_id, $blobUri, $blobSas);

            $tba = $info->only([
                'name', 'description', 'teacher',
                'subject_field_id', 'subject', 'educational_stage_id', 'grade',
                'lecture_type', 'lecture_date', 'locale_id', 'mark', 'channel_id', 'habook_id',
                'student_count', 'irs_count', 'binding_number', 'thumbnail', 'user_id', 'observation_offset'
            ])->toArray();

            $anal     = (array)$info->get('anal');
            $eval     = (array)$info->get('eval');
            $stat     = (array)$info->get('stat');
            $annex    = (array)$info->get('annex');
            $comments = (array)$info->get('comments');

            // User ID
            $user_id = $info->get('user_id');
            // create tba
            $source_url = $blobUri . '/Record/' . $info->get('record_file') . '?' . $blobSas;
            $group_id   = GlobalPlatform::convertAbbrToGroupId($info->get('school_shortcode'));
            if (!$group_id) {
                return $this->setStatus('404')->fail(['message' => 'School shortcode does not exist']);
            }
            if (!empty($comments)) {
                $comments = array_map(function ($comment) use ($group_id) {
                    $comment['group_id'] = $group_id;
                    return $comment;
                }, $comments['list']);
            }

            $sourceImage = $info->get('cover_image');
            $thumbnail   = empty($sourceImage) ? null : $sourceImage;
            $status      = empty($info->get('content_public')) ? 4 : $info->get('content_public');
            // 設定影片狀態
            $content_public = GlobalPlatform::convertChannelStatusToSql((int)$status);

            // 判斷是否有圖片
            if ($thumbnail) {
                $this->downloadThumbnail($blobUri, $thumbnail, $blobSas, $record_id);
            }

            // 判斷 rating_id 是否存在，不存在則給預設值
            $rating_id = $info->get('study');
            if (!$this->ratingService->exists(['id' => $rating_id])) {
                $rating_id = $this->ratingService->firstWhere(['groups_id' => $group_id, 'type' => 0])->id;
            }
            $subject = $info->get('subject');
            if ($this->groupSubjectFieldsService->exists(['id' => $subject])) {
                $tba['subject'] = $this->groupSubjectFieldsService->find($subject)->alias;
                $subject        = ['group_subject_fields_id' => $subject];
            } else {
                $subject = [];
            }

            $convertedGrade            = GlobalPlatform::convertGrade((int)$tba['educational_stage_id'], (int)$tba['grade']);
            $tba['grade']              = is_null($convertedGrade) ? $tba['grade'] : $convertedGrade;
            $double_green_light_status = 0;
            if (!empty($stat)) {
                $double_green_light_status = collect($stat['list'])->whereIn('type', ['TechDex', 'PedaDex'])->where('idx', '>=', 70)->count();
            }
            $tba['double_green_light_status'] = $double_green_light_status === 2 ? 1 : 0;


            $groupChannelData = [
                'ratings_id'   => $rating_id,
                'grades_id'    => $info->get('grade'),
                'group_id'     => $group_id,
                'author_id'    => $user_id,
                'share_status' => 1
            ];
            // 合併影片狀態
            $groupChannelData = array_merge($groupChannelData, $content_public, $subject);

            $channelId    = GlobalPlatform::convertGroupIdToChannelId($group_id);
            $tba          = $this->tbaService->createTba($user_id, $sourceImage, $tba, $anal, $eval, $stat, $annex, $comments, $file_path, $blobUri, $blobSas);
            $resourceData = [
                'user_id'  => $user_id,
                'src_type' => SrcType::Vod,
                'name'     => $tba->id,
                'status'   => 1
            ];
            $this->tbaService->createVideo($user_id, $channelId, $tba, $resourceData, $groupChannelData, $info, $source_url, (int)$status, $onlyVideo);
            $url = getenv('SOKRADEO_URL') . '/exhibition/tbavideo/check-with-habook/?to=' . base64_encode(getenv('SOKRADEO_URL') . '/exhibition/tbavideo#/content/' . $tba->id . '?groupIds=' . $group_id . '&channelId=' . $channelId);

            // Delete record file
            $fileSystem->deleteDirectory($temp);

        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return $this->fail(['message' => 'Data error']);
        }

        return $this->setStatus(200)->success(['url' => $url]);
    }

    /**
     * @param $recorder_id
     * @param $blobUri
     * @param $blobSas
     * @return \Illuminate\Support\Collection
     */
    private function jsonTransform($recorder_id, $blobUri, $blobSas): \Illuminate\Support\Collection
    {
        //886 台灣
        //86 大陸
        //1 美國
        $country_code = null;
        $json_path    = 'temp/lesson/' . $recorder_id;
        $info         = [];
        $annex        = collect();
        if (file_exists($sokInfo = storage_path($json_path . '/Sokrates/SokratesInfo.json'))) {
            $sokInfo      = json_decode(file_get_contents($sokInfo));
            $country_code = $this->groupService->firstWhere(['abbr' => $sokInfo->school_shortcode])->country_code ?? 86;
            if (file_exists($dshead = storage_path($json_path . '/Sokrates/SokratesResults/DShead.json'))) {
                $dshead                      = json_decode(file_get_contents($dshead))[0];
                $sokInfo->observation_offset = $dshead->Index;
            }
            // tba_map
            if (file_exists($eventJson = storage_path($json_path . '/Sokrates/SokratesResults/imgname_vdo.json'))) {
                $imageNameVdo   = json_decode(file_get_contents($eventJson))[0];
                $sokInfo->start = $imageNameVdo->Start;
                $sokInfo->end   = $imageNameVdo->End;
            }

            $user_id            = $this->userService->firstWhere(['habook' => $sokInfo->teammodel_id])->id ?? null;
            $thumbnail          = empty($sokInfo->cover_image) ? null : $sokInfo->cover_image;
            $thumbnail          = is_null($thumbnail) ? null : 'thum.' . pathinfo(storage_path('temp/lesson/' . $sokInfo->cover_image), PATHINFO_EXTENSION);
            $sokInfo->thumbnail = $thumbnail;
            $sokInfo->user_id   = $user_id;
            $sokInfo->habook_id = $sokInfo->teammodel_id;
            $sokInfo->name ?? $sokInfo->name = 'Undefined';

            // annex
            $info = (array)$sokInfo;
            if (file_exists($noteFile = storage_path($json_path . '/Note.pdf'))) {
                $annex->push([
                    'type' => AnnexType::HiTeachNote,
                    'name' => $sokInfo->name,
                    'ext'  => pathinfo($noteFile, PATHINFO_EXTENSION),
                    'path' => 'Note.pdf'
                ]);
                $info['annex']['file'] = $noteFile;
            }
        }
        $info['annex']['list'] = $annex->toArray();


        // tba_analysis_events
        $anal = collect();
        if (file_exists($eventJson = storage_path($json_path . '/Sokrates/SokratesResults/imgname.json'))) {
            $eventJson = collect(json_decode(file_get_contents($eventJson)));

            $eventJson->each(function ($q) use (&$anal) {
                if (isset($q->TimeEnd)) {
                    $anal->push([
                        'event' => $q->Event,
                        'mode'  => $q->Mode ?? null,
                        'data'  => [
                            $q->TimeStrt,
                            $q->TimeEnd,
                            $q->TimePoint,
                        ]
                    ]);
                } else {
                    $anal->push([
                        'event' => $q->Event,
                        'mode'  => $q->Mode ?? null,
                        'data'  => $q->TimePoint,

                    ]);

                }
            });
            $info['anal']['events'] = $anal->toArray();
        }

        $eval             = collect();
        $arrAutoPedaCount = ['LTK' => 1, 'SCD' => 1, 'WCT' => 1, 'WCI' => 1, 'DFI' => 2];
        if (file_exists($AutoPedaJson = storage_path($json_path . '/Sokrates/SokratesResults/Auto_Peda.json'))) {
            $AutoPedaJson = collect(json_decode(file_get_contents($AutoPedaJson)));
            $AutoPedaJson->each(function ($q) use (&$arrAutoPedaCount, $country_code, $eval) {
                if (empty($q->Type) || empty($q->TimePoint)) return;
                if ($arrAutoPedaCount[$q->Type] > 0) {
                    $arrAutoPedaCount[$q->Type]--;
                } else {
                    return;
                }
                $eval->push([
                    'time' => $q->TimePoint,
                    'text' => $this->convertEval($country_code, $q->Type),
                ]);
            });

            $info['eval']['users'] = [
                'comments' => [
                    'tag_id'  => 'STD001',
                    'data'    => $eval->toArray(),
                    'accType' => 'Habook',
                    'accUser' => 'AI001',
                    'name'    => 'AI_Sokrates',
                    'email'   => null
                ],
            ];
        }

        $stat = collect();

        if (file_exists($DataFreqJson = storage_path($json_path . '/Sokrates/SokratesResults/Data_Freq.json'))) {
            $DataFreqJson = collect(json_decode(file_get_contents($DataFreqJson)));
            $DataFreqJson->each(function ($q) use ($stat) {
                $stat->push([
                    'type'     => $q->Event,
                    'freq'     => $q->Freq,
                    'duration' => 0,
                    'idx'      => 0,
                ]);
            });
        }

        //
        if (file_exists($DataPedaDexJson = storage_path($json_path . '/Sokrates/SokratesResults/Data_PedaDex.json'))) {
            $DataPedaDexJson = collect(json_decode(file_get_contents($DataPedaDexJson)));
            $DataPedaDexJson->each(function ($q) use ($stat) {
                $stat->push([
                    'type'     => $q->Event,
                    'freq'     => 0,
                    'duration' => 0,
                    'idx'      => $q->Index,
                ]);
            });
        } else {
            $stat->push([
                'type'     => StatisticType::TechDex,
                'freq'     => 0,
                'duration' => 0,
                'idx'      => 0,
            ], [
                'type'     => StatisticType::PedaDex,
                'freq'     => 0,
                'duration' => 0,
                'idx'      => 0,
            ]);
        }

        if (file_exists($DataTimeJson = storage_path($json_path . '/Sokrates/SokratesResults/Data_Time.json'))) {
            $DataTimeJson = collect(json_decode(file_get_contents($DataTimeJson)));
            $DataTimeJson->each(function ($q) use (&$stat) {
                $stat = $stat->map(function ($item) use ($q) {
                    if ($item['type'] === $q->Event) {
                        $item['duration'] = $q->Duration;
                        return $item;
                    }
                    return $item;
                });
            });
            $info['stat']['list'] = $stat->toArray();
        }
        // Comments
        if (file_exists($SokratesAllComments = storage_path($json_path . '/Sokrates/SokratesAllComments.json'))) {
            $SokratesAllComments = collect(json_decode(file_get_contents($SokratesAllComments)));
            $commentData         = [];
            $SokratesAllComments->each(function ($q) use ($blobSas, $blobUri, &$commentData, &$info) {
                $source_url = $blobUri . '/Sokrates/' . $q . '/comments.json' . '?' . $blobSas;
                $header     = get_headers($source_url);
                if (strpos($header[0], '200')) {
                    collect(json_decode(file_get_contents($source_url)))->each(function ($q) use (&$commentData, &$info) {
                        $attachment = null;
                        if (!is_null($q->imageList)) {
                            $attachment = $q->imageList[0]->ImgSrc;
                        }
                        if (!is_null($q->videoList)) {
                            $attachment = $q->videoList[0]->video;
                        }
                        if (!is_null($q->recList)) {
                            $attachment = $q->recList[0]->rec;
                        }
                        if ($q->expertType !== 'Guest') {
                            $user = $this->userService->loginAsHaBook($q->id, ['name' => $q->name]);
                            if ($q->tableType === 'person') {
                                //暫時註解
//                                if (!$this->tagTypeService->exists(['id' => $q->expPointType])) {
//                                    $this->tagTypeService->create([
//                                        'id' => $q->expPointType, 'content' => json_encode($q->expPointTypeName), 'user_id' => $user->id
//                                    ]);
//                                }
//                                $tagType = $this->tagTypeService->firstWhere(['id' => $q->expPointType]);
//                                if (!$tagType->tags()->where(['id' => $q->expPointTypeId])->exists()) {
//                                    $tagType->tags()->create([
//                                        'id' => $q->expPointTypeId, 'content' => json_encode([
//                                            'name' => $q->tagName, 'description' => $q->tagName
//                                        ])
//                                    ]);
//                                }
                                $this->tagTypeService->updateOrCreate(
                                    ['id' => $q->expPointType, 'user_id' => $user->id],
                                    ['content' => json_encode($q->expPointTypeName)]
                                )->tags()->updateOrCreate(
                                    ['id' => $q->expPointTypeId],
                                    ['content' => json_encode(['name' => $q->tagName, 'description' => $q->tagName])]
                                );
                            }
                        }

                        $commentData[] = [
                            'habook_id'    => $q->id,
                            'nick_name'    => $user->name ?? $q->name,
                            'tag_id'       => $q->expPointTypeId,
                            'user_id'      => $user->id ?? null,
                            'comment_type' => 1,
                            'public'       => 1,
                            'time_point'   => $q->time,
                            'text'         => $q->desc,
                            'attachment'   => $attachment,
                        ];
                    });
                }
            });
            $info['comments']['list'] = $commentData;
        }

        return collect($info);

    }

    /**
     * @param $country_code
     * @param $type
     * @return array|\ArrayAccess|mixed|string
     */
    public function convertEval($country_code, $type)
    {
        switch ($type) {
            case 'LTK':
                $result = Arr::get(['886' => '(P) 學習任務', '86' => '(P) 学习任务', '1' => '(P) Learning assignment'], $country_code);
                break;
            case 'SCD':
                $result = Arr::get(['886' => '(P) 生本決策', '86' => '(P) 生本决策', '1' => '(P) Student - center decision'], $country_code);
                break;
            case 'WCT':
                $result = Arr::get(['886' => '(P) 全班測驗', '86' => '(P) 全班测验', '1' => '(P) Whole -class assessment'], $country_code);
                break;
            case 'WCI':
                $result = Arr::get(['886' => '(P) 全班互動', '86' => '(P) 全班互动', '1' => '(P) Whole -class interaction'], $country_code);
                break;
            default:
                $result = Arr::get(['886' => '(P) 同步差異', '86' => '(P) 同步差异', '1' => '(P) Synchronous differentiated instruction'], $country_code);
        }
        return $result;
    }
}
