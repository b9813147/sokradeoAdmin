<?php

namespace App\Repositories;

use App\Models\DistrictSubject;
use Illuminate\Database\Query\Builder;

class DistrictSubjectRepository extends BaseRepository
{
    /**
     * @var Builder
     */
    protected $model;

    /**
     * DistrictSubjectRepository constructor.
     * @param $model
     */
    public function __construct(DistrictSubject $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $districtId
     * @param string $order
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function districtSubjectList(int $districtId, $order = 'DESC')
    {
        return $this->model->with([
            'districtGroupSubject' => function ($q) {
                $q->with([
                    'groupSubjectField' => function ($q) {
                        $q->withCount('districtChannelContent');
                        $q->with('group');
                    }
                ]);
            }
        ])
            ->orderBy('order', $order)
            ->where('districts_id', $districtId)
            ->get();
    }

    /**
     * 取最大值 order + 1
     * @param int $districtId
     * @return int|mixed
     */
    public function getMaxOrder(int $districtId): int
    {
        return $this->model->where('districts_id', $districtId)->max('order') + 1;
    }

    /**
     *
     * @param array $newIndex
     * @param array $oldIndex
     * @return void
     */
    public function changeSort(array $newIndex, array $oldIndex)
    {
        $newIndex = (object)$newIndex;
        $oldIndex = (object)$oldIndex;

        $this->model->find($newIndex->id)->update(['order' => $oldIndex->order]);
        $this->model->find($oldIndex->id)->update(['order' => $newIndex->order]);
    }

    /**
     * 學區排序
     * @param array $newIndex
     * @param array $oldIndex
     * @param int $districtId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function changeSortByDistrict(array $newIndex, array $oldIndex, int $districtId)
    {
        $newIndex = (object)$newIndex;
        $oldIndex = (object)$oldIndex;

        $this->model->find($newIndex->id)->update(['order' => $oldIndex->order]);
        $this->model->find($oldIndex->id)->update(['order' => $newIndex->order]);

        return $this->model->where('districts_id', $districtId)->get();
    }
}
