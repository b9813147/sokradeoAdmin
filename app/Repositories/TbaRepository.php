<?php

namespace App\Repositories;

use App\Models\GroupChannelContent;
use App\Models\Tba;
use Illuminate\Database\Eloquent\Collection;

class TbaRepository extends BaseRepository
{
    protected $model;

    /**
     * TbaRepository constructor.
     * @param $model
     */
    public function __construct(Tba $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $tba_id
     * @param int $channel_id
     * @param array $attributes
     * @return true|void
     */
    public function createGroupChannelContent(int $tba_id, int $channel_id, $attributes = [])
    {
        $morphToMany = $this->model->query()->find($tba_id)->groupChannels();
        // 判斷影片存不存在
        if ($morphToMany->where('group_channel_id', $channel_id)->exists()) {
            return true;
        }

        return $morphToMany->attach($channel_id, $attributes);
    }

    /**
     * @param int $tba_id
     * @param int $channel_id
     * @return false|int
     */
    public function deleteGroupChannelContent(int $tba_id, int $channel_id)
    {
        $morphToMany = $this->model->query()->find($tba_id)->groupChannels();
        // 判斷影片存不存在
        if ($morphToMany->where('group_channel_id', $channel_id)->exists()) {
            return false;
        }

        return $morphToMany->detach($channel_id);
    }

    /**
     * 取tba 相關 統計數據
     * @param $channel_id
     * @return Tba[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function getTbaStats($channel_id)
    {
        $selectSql = "tbas.habook_id,users.id user_id , users.name, group_concat(tbas.id) tba_ids, tbas.lecture_date, group_channel_contents.group_subject_fields_id,
            sum(IF(group_channel_contents.content_public = 1 and group_channel_contents.content_status = 1, 1, 0)) as public_video,
            sum(IF(group_channel_contents.content_public in (1, 0) and group_channel_contents.content_status in (1, 2), 1, 0)) as video_total,
            sum(IF(tbas.double_green_light_status = 1, 1, 0)) as double_green_light,
            sum(IF(group_channel_contents.content_public in (1, 0) and group_channel_contents.content_status in (1, 2), hits,0)) as his_total
       ";


//        $selectStatisticsSql = 'MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T
//                               ,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P
//                               ,tba_statistics.tba_id';

        return $this->model->query()
            ->selectRaw($selectSql)
//            ->with([
//                'tbaStatistics' => function ($q) use ($selectStatisticsSql) {
//                    $q->selectRaw($selectStatisticsSql)
//                        ->groupBy('tba_statistics.tba_id');
//                },
//            ])
            ->selectRaw($selectSql)
            ->join('group_channel_contents', 'tbas.id', 'group_channel_contents.content_id')
            ->join('users', 'tbas.habook_id', 'users.habook')
            ->where('group_channel_contents.group_channel_id', $channel_id)
            ->whereNotNull('tbas.habook_id')
            ->groupBy('tbas.habook_id')->get();
    }

    /**
     * @param int $channel_id
     * @param null $date
     * @param null $search
     * @param null $column
     * @param int $page
     * @return mixed
     */
    public function getTbaStatsOrFilter(int $channel_id, $date = null, $search = null, $column = null, int $page = 15)
    {
        $selectSql = "tbas.habook_id,users.id user_id,users.name, group_concat(tbas.id) tba_ids, tbas.lecture_date,group_channel_contents.group_subject_fields_id,group_channel_contents.grades_id, group_channel_contents.ratings_id,
             sum(IF(group_channel_contents.content_public = 1 and group_channel_contents.content_status = 1, 1, 0)) as public_video,
             sum(IF(group_channel_contents.content_public in (1, 0) and group_channel_contents.content_status in (1, 2), 1, 0)) as video_total,
             sum(IF(tbas.double_green_light_status = 1, 1, 0)) as double_green_light,
             sum(IF(group_channel_contents.content_public in (1, 0) and group_channel_contents.content_status in (1, 2), hits,0)) as his_total
       ";
        $tba       = $this->model->query();

        if (!empty($date)) {
            $tba->whereBetween('lecture_date', $date);
        }

        if (!empty($search) && !empty($column)) {
            $tba->where($column, $search);
//            $tba->orWhere('users.name', 'like', "$search%");
//            $tba->orWhere('tbas.habook_id', 'like', "$search%");
        }

        return $tba->selectRaw($selectSql)
            ->join('group_channel_contents', 'tbas.id', 'group_channel_contents.content_id')
            ->join('users', 'tbas.habook_id', 'users.habook')
            ->where('group_channel_contents.group_channel_id', $channel_id)
            ->whereNotNull('tbas.habook_id')
            ->groupBy('tbas.habook_id')->get();
//            ->paginate($page);
    }

    /**
     * @param int $userId
     * @param $tba
     * @return Tba|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function createTba(int $userId, $tba)
    {
        $tba['user_id'] = $userId;
        return $this->model->query()->updateOrCreate(['binding_number' => $tba['binding_number']], $tba);
    }

    /**
     * @param int $groupChannelId
     * @param int $contentId
     * @return Tba[]|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function getTbaInfo(int $groupChannelId, int $contentId)
    {
        $contentId                 = GroupChannelContent::query()->where('group_channel_id', $groupChannelId)->where('content_id', $contentId)->pluck('content_id');
        $concernedStatisticalTypes = [49, 50, 53, 61, 52, 54, 31, 47, 48]; # In order based on Norm table appearance
        return Tba::query()->with([
            'tbaEvaluateEvents' => function ($q) {
                $q->with([
                    'tbaEvaluateEventMode'  => function ($q) {
                        $q->select('mode', 'event', 'id');
                    },
                    'tbaEvaluateUser'       => function ($q) {
                        $q->with([
                            'user' => function ($q) {
                                $q->select('name', 'id', 'habook');
                            }
                        ])->where('identity', '!=', 'G');
                    },
                    'tbaEvaluateEventFiles' => function ($q) {
                        $q->select('name', 'ext', 'image_url', 'tba_evaluate_event_id');
                    },
                ])->whereNotNull('tba_evaluate_user_id')->orderBy('time_point', 'ASC')->selectRaw("text,time_point, tba_id, tba_evaluate_event_mode_id, tba_evaluate_user_id, id");
            },
            'user'              => function ($q) {
                $q->select('name', 'id', 'habook');
            },
            'tbaAnnexs'         => function ($q) {
                $q->select('type', 'tba_id');
            },
            'groupChannels'     => function ($q) use ($groupChannelId) {
                $q->where('id', $groupChannelId);
            },
            'tbaStatistics'     => function ($q) use ($concernedStatisticalTypes) {
                $q->whereIn('type', $concernedStatisticalTypes)->distinct()->get();
            }
        ])->where('id', $contentId)->get();
    }

}
