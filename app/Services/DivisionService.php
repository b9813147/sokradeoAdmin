<?php

namespace App\Services;

use App\Repositories\DivisionRepository;

class DivisionService extends BaseService
{
    protected $repository;

    /**
     * DivisionService constructor.
     * @param DivisionRepository $repository
     */
    public function __construct(DivisionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 查詢分組內的使者
     * @param int $group_id
     * @return \App\Models\Division|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function findByUsersAndTbas(int $group_id)
    {
        return $this->repository->findByUsersAndTbas($group_id);
    }

    /**
     * 同步使用者
     * @param int $id
     * @param array $user_ids
     * @return bool
     */
    public function syncUsers(int $id, array $user_ids): bool
    {
        $isSuccessful = true;
        try {
            $this->repository->find($id)->users()->sync($user_ids);
        } catch (\Exception $exception) {
            $isSuccessful = false;
        }
        return $isSuccessful;

    }
}
