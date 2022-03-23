<?php

namespace App\Http\Controllers\Api\V1\Division;

use App\Enums\NotificationMessageType;
use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\Api\V1\Division\DivisionCollection;
use App\Http\Resources\Api\V1\Division\TbaCollection;
use App\Http\Resources\Api\V1\Division\UserCollection;
use App\Models\GroupChannelContent;
use App\Models\Tba;
use App\Services\DivisionService;
use App\Services\GroupChannelService;
use App\Services\GroupService;
use App\Services\NotificationMessageService;
use App\Services\UserService;
use App\Types\Group\DutyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DivisionController extends Controller
{
    /**
     * @var DivisionService
     */
    protected $divisionService;
    /**
     * @var GroupChannelService
     */
    protected $groupChannelService;
    /**
     * @var GroupService
     */
    protected $groupService;
    /**
     * @var NotificationMessageService
     */
    protected $notificationMessageService;
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * DivisionController constructor.
     */
    public function __construct(DivisionService $divisionService, GroupService $groupService, GroupChannelService $groupChannelService, NotificationMessageService $notificationMessageService, UserService $userService)
    {
        $this->divisionService            = $divisionService;
        $this->groupService               = $groupService;
        $this->groupChannelService        = $groupChannelService;
        $this->notificationMessageService = $notificationMessageService;
        $this->userService                = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $divisionData = $request->only('title', 'group_id');
            $users        = $request->input('users');
            $tbas         = $request->input('tbas');

            $channel_id   = GlobalPlatform::convertGroupIdToChannelId($request->input('group_id'));
            $divisionInfo = $this->divisionService->create($divisionData);
            $divisionInfo->users()->attach($users);
            $this->groupChannelService->setContent($channel_id, $tbas, ['division_id' => $divisionInfo->id]);
            Log::info('sendNotify', [
                'status'   => GlobalPlatform::sendNotify($request->input('group_id'), $users, NotificationMessageType::REVIEW),
                'group_id' => $request->input('group_id'), 'user_ids' => $users
            ]);
            return $this->success('');
        } catch (\Exception $exception) {
            return $this->fail($exception->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $group_id): \Illuminate\Http\JsonResponse
    {
        try {
            $select       = ['ratings.name as rating_name', 'group_subject_fields.alias as alias', 'users.name as users_name', 'users.habook', 'tbas.name as tba_name', 'group_channel_contents.division_id', 'tbas.id'];
            $divisionInfo = $this->divisionService->findByUsersAndTbas($group_id);
            $users        = $this->groupService->find($group_id)->users()->wherePivotIn('member_duty', [DutyType::Admin, DutyType::Expert])->get();
            // 有時間在優化此段
            $tbas = Tba::query()
                ->select($select)
                ->join('group_channel_contents', 'tbas.id', 'group_channel_contents.content_id')
                ->join('users', 'tbas.user_id', 'users.id')
                ->join('group_subject_fields', 'group_channel_contents.group_subject_fields_id', 'group_subject_fields.id')
                ->join('ratings', 'group_channel_contents.ratings_id', 'ratings.id')
                ->where('group_channel_contents.group_id', $group_id)
                ->orderBy('group_channel_contents.group_subject_fields_id', 'DESC')
                ->orderBy('group_channel_contents.ratings_id', 'DESC')
                ->get();

            $data = [
                'users'     => new UserCollection($users),
                'divisions' => new DivisionCollection($divisionInfo),
                'tbas'      => new TbaCollection($tbas),

            ];
            return $this->success($data);
        } catch (\Exception $exception) {
            return $this->fail($exception->getMessage());
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $divisionData = $request->only('title', 'group_id');
            $users        = $request->input('users');
            $tbas         = $request->input('tbas');

            $channel_id = GlobalPlatform::convertGroupIdToChannelId($request->input('group_id'));
            $this->divisionService->update($id, $divisionData);
            $this->divisionService->syncUsers($id, $users);
            // 將賦予分組的設定 null
            $this->groupChannelService->whereContent($channel_id, 'division_id', $id)->each(function ($tba) {
                $tba->pivot->division_id = null;
                $tba->pivot->save();
            });

            // 依據指定tba更新分組
            $this->groupChannelService->setContent($channel_id, $tbas, ['division_id' => $id]);
            Log::info('sendNotify', [
                'status'   => GlobalPlatform::sendNotify($request->input('group_id'), $users, NotificationMessageType::REVIEW),
                'group_id' => $request->input('group_id'), 'user_ids' => $users
            ]);
            return $this->setStatus(204)->success(null);
        } catch (\Exception $exception) {
            return $this->setStatus(412)->fail($exception->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $divisionInfo = $this->divisionService->find($id);
            $channel_id   = GlobalPlatform::convertGroupIdToChannelId($divisionInfo->group_id);
            // 將賦予分組的設定 null
            $this->groupChannelService->whereContent($channel_id, 'division_id', $id)->each(function ($tba) {
                $tba->pivot->division_id = null;
                $tba->pivot->save();
            });

            $divisionInfo->users()->sync([]);
            $this->divisionService->destroy($id);
            return $this->success('');
        } catch (\Exception $exception) {
            return $this->fail($exception->getMessage());
        }
    }
}
