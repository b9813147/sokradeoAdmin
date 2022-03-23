<?php

namespace App\Services;

use App\Helpers\Code\ImageUploadHandler;
use App\Helpers\Custom\GlobalPlatform;
use App\Repositories\GroupChannelContentRepository;

class GroupChannelContentService extends BaseService
{
    use ImageUploadHandler;

    protected $repository;
    protected $groupSubjectFieldsService;
    protected $ratingService;
    protected $groupService;
    /**
     * @var TbaService
     */
    protected $tbaService;

    public function __construct(
        GroupChannelContentRepository $groupChannelContentRepository,
        GroupSubjectFieldsService     $groupSubjectFieldsService,
        RatingService                 $ratingService,
        GroupService                  $groupService,
        TbaService                    $tbaService
    )
    {
        $this->repository                = $groupChannelContentRepository;
        $this->groupSubjectFieldsService = $groupSubjectFieldsService;
        $this->ratingService             = $ratingService;
        $this->groupService              = $groupService;
        $this->tbaService                = $tbaService;
    }

    /**
     * @param $group_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getChannelContentList($group_id)
    {
        return $this->repository->getGroupChannelContentList($group_id);
    }

    /**
     * @param $groupId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getGroupChannelContentAndTbaByGroupChannelId($groupChannelId)
    {
        return $this->repository->getGroupChannelContentAndTbaByGroupChannelId($groupChannelId);
    }

    /**
     * 更新groupChannelContent 與 Tba
     *
     * @param object $request
     * @param integer $group_id
     * @param null $thumbnail
     * @throws \Exception
     */
    public function updateGroupChannelContentAndTba(object $request, int $group_id, $thumbnail = null)
    {
        return $this->repository->updateGroupChannelContentAndTba($request, $group_id, $thumbnail);
    }

    /**
     * 刪除頻道內容
     *
     * @param integer $group_id
     * @param integer $contentId
     * @return mixed
     */
    public function deleteGroupChannelContent(int $group_id, int $contentId)
    {
        return $this->repository->deleteWhere(['group_id' => $group_id, 'content_id' => $contentId]);
    }


    /**
     * @param array $content_status
     * @param int $content_public
     * @return int
     */
    public function getGroupVideoTotal(array $content_status, int $content_public)
    {
        return $this->repository->videoTotal($content_status, $content_public);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function getVideoTotalByGroup()
    {
        return $this->repository->videoTotalByGroup();
    }

    /**
     * 分享影片至其他 頻道
     * @param int $group_id
     * @param $originalData
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function shareVideo(int $group_id, $originalData = [])
    {

        $alias                     = $originalData->alias['text'] ?? null;
        $rating_id                 = $this->ratingService->firstWhere(['groups_id' => $group_id, 'type' => 0])->id;
        $groupSubjectFieldsService = $this->groupSubjectFieldsService->firstWhere(['groups_id' => $group_id, 'alias' => $alias])->id ?? null;
        $review_status             = (int)$this->groupService->findBy('id', $group_id)->review_status;
        $channelId                 = GlobalPlatform::convertGroupIdToChannelId($group_id);

        return $this->tbaService->createGroupChannelContent($originalData->id, $channelId, [
            'group_id'                => $group_id,
            'content_status'          => $review_status === 0 ? 1 : 2,
            'group_subject_fields_id' => $groupSubjectFieldsService,
            'grades_id'               => $originalData->grade['value'] ?? null,
            'ratings_id'              => $rating_id,
            'author_id'               => auth()->id(),
            'share_status'            => 0,
        ]);
    }
}
