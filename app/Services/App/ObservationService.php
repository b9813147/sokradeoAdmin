<?php

namespace App\Services\App;


use App\Helpers\Custom\GlobalPlatform;
use App\Libraries\Azure\Blob;
use App\Models\ObservationClass;
use App\Services\GroupSubjectFieldsService;
use App\Services\ObservationClassService;
use App\Services\RatingService;
use App\Services\TagTypeService;
use App\Services\TbaService;
use App\Services\UserService;
use App\Types\Device\DeviceType;
use App\Types\Src\SrcType;

class ObservationService
{
    protected $observationClassService;
    protected $groupSubjectFieldsService;
    protected $ratingService;
    protected $tbaService;
    protected $userService;
    protected $tagTypeService;

    /**
     * @param ObservationClassService $observationClassService
     * @param GroupSubjectFieldsService $groupSubjectFieldsService
     * @param RatingService $ratingService
     * @param TbaService $tbaService
     * @param UserService $userService
     * @param TagTypeService $tagTypeService
     */
    public function __construct(
        ObservationClassService   $observationClassService,
        GroupSubjectFieldsService $groupSubjectFieldsService,
        RatingService             $ratingService,
        TbaService                $tbaService,
        UserService               $userService,
        TagTypeService            $tagTypeService
    )
    {
        $this->observationClassService   = $observationClassService;
        $this->groupSubjectFieldsService = $groupSubjectFieldsService;
        $this->ratingService             = $ratingService;
        $this->tbaService                = $tbaService;
        $this->userService               = $userService;
        $this->tagTypeService            = $tagTypeService;
    }


    /**
     * @param $id
     * @return \App\Models\Tba|array|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function createLesson($id)
    {
        $isSuccessful = [];
        try {
            $blobClient = new Blob(getenv('BLOB_ACCOUNT'), getenv('BLOB_KEY'), getenv('ENDPOINT'));
            $blobSas    = $blobClient->getSas('', getenv('BLOB_VIDEO_CONTAINER'), 1, 'rwacl', 'c');
            $info       = $this->observationClassService->getObservationClassWithUsers($id);
            if ($info instanceof ObservationClass) {
                $blobUri    = $blobClient->getContainerUrl(getenv('BLOB_VIDEO_CONTAINER'));
                $commentUrl = $blobUri . '/observation/' . $info->binding_number . '/Sokrates/';
                $group_id   = $info->group_id;

                $tba            = $info->only([
                    'name', 'description', 'teacher',
                    'subject', 'educational_stage_id', 'grade',
                    'lecture_type', 'lecture_date', 'locale_id', 'habook_id',
                    'binding_number', 'thumbnail', 'user_id'
                ]);
                $tba['subject'] = $this->groupSubjectFieldsService->find($info->group_subject_field_id)->alias;
                $tba['grade']   = $info->grade_id;
                $comments       = $this->comment($info->observationUsers, $blobSas, $commentUrl);

                // User ID
                $user_id = $info->user_id;

                if (!empty($comments)) {
                    $comments = array_map(function ($comment) use ($group_id) {
                        $comment['group_id'] = $group_id;
                        return $comment;
                    }, $comments['list']);
                }

                // 設定影片狀態
                $content_status = (int)$info->content_public;
                $content_public = GlobalPlatform::convertChannelStatusToSql((int)$info->content_public);
                $rating_id      = $this->ratingService->firstWhere(['groups_id' => $group_id, 'type' => 0])->id;

                $convertedGrade                   = GlobalPlatform::convertGrade((int)$tba['educational_stage_id'], (int)$tba['grade']);
                $tba['grade']                     = is_null($convertedGrade) ? $tba['grade'] : $convertedGrade;
                $tba['double_green_light_status'] = 0;

                $groupChannelData = [
                    'ratings_id'   => $rating_id,
                    'grades_id'    => $info->grade_id,
                    'group_id'     => $group_id,
                    'author_id'    => $user_id,
                    'share_status' => 1
                ];
                // 合併影片狀態
                $groupChannelData = array_merge($groupChannelData, $content_public, ['group_subject_fields_id' => $info->group_subject_field_id]);
                $channelId        = GlobalPlatform::convertGroupIdToChannelId($group_id);
                $tba              = $this->tbaService->createTba($user_id, '', $tba, [], [], [], [], $comments, '', $commentUrl, $blobSas, DeviceType::App);
                $resourceData     = [
                    'user_id'  => $user_id,
                    'src_type' => SrcType::Vod,
                    'name'     => $tba->id,
                    'status'   => 1
                ];
                $this->tbaService->createVideo($user_id, $channelId, $tba, $resourceData, $groupChannelData, collect($info), '', $content_status);
                $isSuccessful = $tba;
            }
        } catch (\Exception $exception) {
            \Log::info('App Lesson', ['status' => 0, 'message' => $exception->getMessage()]);
            $isSuccessful = [];
        }
        return $isSuccessful;
    }

    /**
     * Comment collect
     * @param $allCommentUsers
     * @param $blobSas
     * @param $blobUri
     * @return array
     */
    private function comment($allCommentUsers, $blobSas, $blobUri): array
    {
        // Comments
        $commentData = [];
        $allCommentUsers->each(function ($q) use ($blobSas, $blobUri, &$commentData) {
            if (is_null($q->user)) {
                $source_url = $blobUri . $q->guest . '/comments.json' . '?' . $blobSas;
            } else {
                $source_url = $blobUri . $q->user->habook . '/comments.json' . '?' . $blobSas;
            }

            $header = get_headers($source_url);
            \Log::info('comment', ['status' => strpos($header[0], '200')]);
            if (strpos($header[0], '200')) {
                collect(json_decode(file_get_contents($source_url)))->each(function ($q) use (&$commentData) {
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
        $comments['list'] = $commentData;

        return $comments;
    }
}
