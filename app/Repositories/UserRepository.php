<?php

namespace App\Repositories;

use App\Models\User;
use App\Types\Auth\AccountType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use LogicException;

class UserRepository extends BaseRepository
{
    /**
     * @var User
     * @var $model \Illuminate\Database\Query\Builder
     */
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * 取得使用者相關細部資訊
     * @param int $userId
     *
     * @return Builder|Builder[]|Collection|Model|User[]
     */
    public function getUser(int $userId)
    {
        return $this->model->with('roles', 'resources', 'videos', 'tbas', 'groups')->findOrFail($userId);
    }

    /**
     * @param $AccType
     * @param $accId
     *
     * @return User|User[]|Builder|Builder[]|Collection|Model
     */
    public function getUserByAcc($AccType, $accId)
    {
        $colAcc = AccountType::getDbColNameByAccType($AccType);
        $user   = $this->model->query()->where($colAcc, $accId)->firstOrFail();

        return $this->getUser($user->id);
    }

    public function getUserByClientAcc($clientId, $clientUser)
    {
        $user = $this->model->query()->where('client_id', $clientId)->where('client_user', $clientUser)->firstOrFail();
        return $this->getUser($user->id);
    }

    /**
     * @param int $userId
     * @param array $userData
     * @param array $roles
     * @return User|User[]|Builder|Builder[]|Collection|Model|null
     */
    public function setUser(int $userId, array $userData, array $roles)
    {
        $user = $this->model::query()->findOrFail($userId);
        $user->fill($userData);
        $user->save();
        $user->roles()->sync($roles);
        return $user;

    }

    //
    public function createUserByAcc($accType, $accId, $accData)
    {
        $colAcc = AccountType::getDbColNameByAccType($accType);
        if ($this->model->query()->where($colAcc, $accId)->exists()) {
            throw new LogicException('account is already exist');
        }
        $accData[$colAcc] = $accId;
        $user             = $this->model->create($accData);
        return $this->getUser($user->id);
    }

    //
    public function createUserByClientAcc($clientId, $clientUser, $accData)
    {
        if ($this->model->query()->where('client_id', $clientId)->where('client_user', $clientUser)->exists()) {
            throw new LogicException('account is already exist');
        }
        $accData['client_id']   = $clientId;
        $accData['client_user'] = $clientUser;
        $user                   = $this->create($accData);
        return $this->getUser($user->id);
    }

    //
    public function getUserOrCreateByAcc($accType, $accId, $accData)
    {
        $colAcc = AccountType::getDbColNameByAccType($accType);
        $user   = $this->model->query()->updateOrCreate([$colAcc => $accId], $accData);
        return $this->getUser($user->id);
    }

    //
    public function getUserOrCreateByClientAcc($clientId, $clientUser, $accData)
    {
        $user = $this->model->query()->updateOrCreate(['client_id' => $clientId, 'client_user' => $clientUser], $accData);
        return $this->getUser($user->id);
    }

    //
    public function searchUsers($name)
    {
        if (empty($name)) {
            return [];
        }

        return $this->model->query()->select('id', 'name')->where('name', 'like', $name . '%')->get();
    }

    /**
     * @param int $perPage
     * @param null $search
     * @return array
     */
    public function getUserAndPaginate($perPage = Null, $search = Null)
    {

        $data = $this->model->query()->where(function ($q) use ($search) {
            if ($search) {
                $q->where('habook', 'like binary', '%' . $search . '%')
                    ->orWhere('client_id', 'like binary', '%' . $search . '%')
                    ->orWhere('client_user', 'like binary', '%' . $search . '%')
                    ->orWhere('email', 'like binary', '%' . $search . '%');
            }
        })->paginate($perPage);


        $response = [
            'data'     => $data,
            'paginate' => [
                'total'        => $data->total(),
                'per_page'     => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'from'         => $data->firstItem(),
                'to'           => $data->lastItem(),
            ]
        ];
        return $response;
    }

    public function search($search)
    {
        $data = $this->model->query()->where(function ($q) use ($search) {
            if ($search) {
                $q->where('habook', 'like binary', '%' . $search . '%')
                    ->orWhere('client_id', 'like binary', '%' . $search . '%')
                    ->orWhere('client_user', 'like binary', '%' . $search . '%')
                    ->orWhere('email', 'like binary', '%' . $search . '%');
            }
        })->get();

        return $data;
    }

}
