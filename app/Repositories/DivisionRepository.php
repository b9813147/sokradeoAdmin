<?php

namespace App\Repositories;


use App\Models\Division;

class DivisionRepository extends BaseRepository
{
    protected $model;

    /**
     * DivisionRepository constructor.
     * @param Division $model
     */
    public function __construct(Division $model)
    {
        $this->model = $model;
    }

    /**
     * 查詢分組內的使者
     * @param int $group_id
     * @return Division|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function findByUsersAndTbas(int $group_id)
    {
        return $this->model->with('users', 'tbas')->where('group_id', $group_id)->get();
    }

}
