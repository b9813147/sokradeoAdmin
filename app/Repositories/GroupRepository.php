<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\GroupChannel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GroupRepository extends BaseRepository
{
    protected $model;

    public function __construct(Group $group)
    {
        $this->model = $group;
    }

    public function getGroupList($column = ['*'])
    {
        return $this->model::query()->orderByDesc('id')->get($column);
    }

    /**
     * 用channelId 去找 groupID
     *
     * @param $channelId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function getChannelByGroupId($channelId)
    {
        return GroupChannel::query()->with('group')->where('id', $channelId)->first();
    }

    /**
     * 同時更新 groups 與 groupChannels
     *
     * @param array $data
     * @param null $files
     * @param null $name
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function updateGroupAndGroupChannel(array $data, $files = null, $name = null)
    {
        $result = $this->getGroupInfo($data['groupId']);

        if (!is_null($files)) {
            $data['thumbnail'] = $name;
            $result->update($data);
            $result->channels->update($data);
            $result->groupLangs->each(function ($groupLang) use ($data) {
                $groupLang->update($data);
            });
            return $result;
        }

        $result->update($data);
        $result->channels->update($data);
        $result->groupLangs->each(function ($groupLang) use ($data) {
            $groupLang->update($data);
        });
        return $result;
    }

    /**
     * 頻道數量
     * @param array $public
     * @return int
     */
    public function groupTotal(array $public)
    {
        return $this->model->query()->where($public)->count();
    }


    /**
     * 取頻道相關細部資訊
     *
     * @param int $group_id
     * @return Group|Group[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getGroupInfo(int $group_id)
    {
        return $this->model->query()->with([
            'users', 'channels', 'groupLangs', 'notificationMessages',
            'tagTypes' => function ($q) {
                $q->where('status', 1);
                $q->with([
                    'tags' => function ($q) {
                        $q->where('status', 1);
                    }
                ]);
            }
        ])
            ->findOrFail($group_id);
    }

    /**
     * 更改活動狀態
     * @return bool
     */
    public function setEventChannel(): bool
    {
        try {
            // 當前時間
            $current = Carbon::now()->format('Y-m-d');
            $this->model::query()->select('event_data->eventStage as eventStage', 'id')->with('channels')
                ->whereJsonLength('event_data', '>', 0)->get()->each(function ($q) use ($current) {
                    $eventStage = collect(json_decode($q->eventStage))
                        ->where('endDate', '>=', $current)
                        ->where('stageOrder', '!=', 0)
                        ->sortBy('stageOrder');
                    if ($eventStage->isEmpty()) {
                        return $q->channels()->update(['stage' => 3]);
                    }
                    return $q->channels()->update(['stage' => $eventStage->first()->stageOrder]);
                });
        } catch (\Exception $exception) {
            Log::info('setEventChannel', [$exception->getMessage()]);
            return false;
        }
        return true;
    }
}
