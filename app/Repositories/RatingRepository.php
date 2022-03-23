<?php

namespace App\Repositories;

use App\Models\Rating;
use Illuminate\Database\Query\Builder;

class RatingRepository extends BaseRepository
{

    /**
     * @var Rating
     * @var $model Builder
     */
    protected $model;


    /**
     * RatingRepository constructor.
     * @param $model
     */
    public function __construct(Rating $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $column
     * @param string $value
     * @param string $sort
     * @param string[] $columns
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getByForSort(string $column, string $value, $sort = 'DESC', $columns = ['*'])
    {
        return $this->model->query()->where($column, $value)->orderBy('type', $sort)->get($columns);
    }

    /**
     * @param array $where
     * @param string $sort
     * @param string[] $columns \
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getByUseStatistics(array $where, $sort = 'DESC', $columns = ['*'])
    {
        return $this->model->query()->with([
            'groupChannelContent'    => function ($q) {
                $q->selectRaw("count(ratings_id) total , ratings_id")->groupBy('ratings_id');
            },
            'districtChannelContent' => function ($q) {
                $q->selectRaw("count(ratings_id) total , ratings_id")->groupBy('ratings_id');
            }
        ])->where($where)->orderBy('type', $sort)->get($columns);
    }

    public function getTypeMax(array $where)
    {
        return $this->model->query()->where($where)->max('type');
    }

    /**
     * 頻道排序
     * @param array $newIndex
     * @param array $oldIndex
     * @param int $group_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function changeSort(array $newIndex, array $oldIndex, int $group_id)
    {
        $newIndex = (object)$newIndex;
        $oldIndex = (object)$oldIndex;

        $this->model->query()->find($newIndex->id)->update(['type' => $oldIndex->type]);
        $this->model->query()->find($oldIndex->id)->update(['type' => $newIndex->type]);

        return $this->getByUseStatistics(['groups_id' => $group_id, ['type', '>', 1]]);
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

        $this->model->query()->find($newIndex->id)->update(['type' => $oldIndex->type]);
        $this->model->query()->find($oldIndex->id)->update(['type' => $newIndex->type]);

        return $this->getByUseStatistics(['districts_id' => $districtId, ['type', '>', 1]]);
    }


}
