<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Types\Auth\AccountType;
use Illuminate\Database\Query\Builder;

class UserService extends BaseService
{
    protected $repository;

    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }

    /**
     * @param $perPage
     * @param $search
     * @return array
     */
    public function getUserAndPaginate($perPage, $search): ?array
    {
        return $this->repository->getUserAndPaginate($perPage, $search);
    }

    public function getUserAll()
    {
        return $this->repository->all();
    }

    public function getUser($user_id)
    {
        return $this->repository->getUser($user_id);
    }

    /**
     * @param int $userId
     * @param array $userData
     * @param array $roles
     * @return \App\Models\User|null
     */
    public function setUser(int $userId, array $userData, array $roles): ?\App\Models\User
    {
        return $this->repository->setUser($userId, $userData, $roles);
    }

    /**
     * @param $accId
     * @param array $accData
     * @return \App\Models\User|null|Builder[]
     * \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function loginAsHaBook($accId, array $accData): ?\App\Models\User
    {
        return $this->repository->getUserOrCreateByAcc(AccountType::Habook, $accId, $accData);
    }
}
