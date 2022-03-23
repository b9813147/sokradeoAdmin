<?php

namespace App\Repositories;

use App\Models\GroupSubjectField;
use Illuminate\Database\Query\Builder;

class GroupSubjectFieldsRepository extends BaseRepository
{
    /**
     * @var GroupSubjectField
     * @var $model Builder
     */
    protected $model;

    /**
     * GroupSubjectFieldsRepository constructor.
     * @param $model
     */
    public function __construct(GroupSubjectField $model)
    {
        $this->model = $model;
    }

    /**
     * 取得學科資料與學科欄位
     *
     * @param int $groupsId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getSubject(int $groupsId)
    {
        return $this->model->query()->with([
            'subjectFields'          => function ($q) {
            },
            'groupChannelContent'    => function ($q) {
                $q->selectRaw("count(group_subject_fields_id) total , group_subject_fields_id")->groupBy('group_subject_fields_id');
            },
            'districtChannelContent' => function ($q) {
                $q->selectRaw("count(group_subject_fields_id) total , group_subject_fields_id")->groupBy('group_subject_fields_id');
            }
        ])->where('groups_id', $groupsId)->orderBy('order', 'ASC')->get();
    }

    /**
     * 取最大值Order
     *
     * @param int $group_id
     * @return mixed
     */
    public function getMaxOrder(int $group_id)
    {
        return $this->model->query()->where('groups_id', $group_id)->max('order') + 1;
    }

    /**
     * @param array $newIndex
     * @param array $oldIndex
     * @return void
     */
    public function changeSort(array $newIndex, array $oldIndex)
    {
        $newIndex = (object)$newIndex;
        $oldIndex = (object)$oldIndex;

        $this->model->query()->find($newIndex->id)->update(['order' => $oldIndex->order]);
        $this->model->query()->find($oldIndex->id)->update(['order' => $newIndex->order]);
    }

    /**
     * 取得頻道所有學科
     * @param object $groupsIds
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function groupSubjects(object $groupsIds)
    {
        return $this->model->query()->with([
            'subjectFields', 'group',
            'districtGroupSubject' => function ($q) {
                $q->with('districtSubject');
            }
        ])->
        whereIn('groups_id', $groupsIds)->orderBy('order', 'DESC')->get();
    }
}
