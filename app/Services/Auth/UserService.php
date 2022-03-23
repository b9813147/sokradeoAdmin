<?php

namespace App\Services\Auth;

use App\Repositories\UserRepository;
use App\Types\Auth\AccountType;

class UserService
{
    private $userRepo = null;

    //
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }


    /**
     * @param $accId
     * @param $accData
     * @return \App\Models\User|\App\Models\User[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|
     * \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function loginAsHaBook($accId, $accData)
    {
        return $this->userRepo->getUserOrCreateByAcc(AccountType::Habook, $accId, $accData);
    }

    /**
     * @param $user
     */
    public function signIn($user)
    {

        auth()->loginUsingId($user->id);

    }

    /**
     * @param $userId
     * @param $userData
     * @param $roles
     * @return \App\Models\User|null
     */
    public function setUser($userId, $userData, $roles): ?\App\Models\User
    {
        return $this->userRepo->setUser($userId, $userData, $roles);
    }

}
