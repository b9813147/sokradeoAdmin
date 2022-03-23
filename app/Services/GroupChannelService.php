<?php

namespace App\Services;

use App\Repositories\GroupChannelRepository;


class GroupChannelService extends BaseService
{
    protected $repository;

    public function __construct(GroupChannelRepository $groupChannelRepository)
    {
        $this->repository = $groupChannelRepository;
    }

    /**
     * 取得屬於 groupId 的頻道
     *
     * @param $group_id
     * @return mixed
     */
    public function getGroupIdByChannel($group_id)
    {
        return $this->repository->findBy('group_id', $group_id);
    }

    /**
     * 取得頻道下的所有影片
     *
     * @param int $channelId
     * @return \App\Models\GroupChannel|\App\Models\GroupChannel[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getGroupChannelByTba(int $channelId)
    {
        return $this->repository->getGroupChannelByTba($channelId);
    }

    /**
     * 取頻頻道成員
     * @param array $channel_ids
     * @return \Illuminate\Support\Collection|null
     */
    public function getChannelForUser(array $channel_ids): ?\Illuminate\Support\Collection
    {
        return $this->repository->getChannelForUser($channel_ids);
    }

    /**
     * @param int $notification_message_id
     * @param string $vale
     * @return bool|int
     */
    public function updateByNotificationId(int $notification_message_id, string $vale)
    {
        return $this->repository->updateByNotificationId($notification_message_id, $vale);
    }

    /**
     * 更新中間表
     * @param int $id
     * @param mixed $content_id
     * @param array $attributes
     * @return bool
     */
    public function setContent(int $id, $content_id, array $attributes): bool
    {
        $isSuccessful = true;
        try {
            $this->repository->find($id)->tbas()->updateExistingPivot($content_id, $attributes);
        } catch (\Exception $exception) {
            $isSuccessful = false;
        }
        return $isSuccessful;
    }

    /**
     * 過濾中間表
     * @param int $id
     * @param string $columns
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function whereContent(int $id, string $columns, string $value): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->find($id)->tbas()->wherePivot($columns, $value)->get();
    }
}
