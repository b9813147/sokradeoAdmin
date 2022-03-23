<?php

namespace App\Http\Controllers\Api\V1\Notification;


use App\Enums\NotificationMessageType;
use App\Http\Controllers\Api\V1\Controller;
use App\Services\GroupChannelService;
use App\Services\NotificationMessageService;
use Illuminate\Http\Request;
use Mockery\Exception;

class EventNotificationController extends Controller
{
    /**
     * @var NotificationMessageService
     */
    protected $notificationMessageService;
    /**
     * @var GroupChannelService
     */
    protected $groupChannelService;

    /**
     * NotificationController constructor.
     */
    public function __construct(NotificationMessageService $notificationMessageService, GroupChannelService $groupChannelService)
    {
        $this->notificationMessageService = $notificationMessageService;
        $this->groupChannelService        = $groupChannelService;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $notificationInfo = $this->notificationMessageService->find($id);
            return $this->setStatus(200)->success($notificationInfo);

        } catch (\Exception $exception) {
            return $this->setStatus(401)->fail(['message' => $exception->getMessage()]);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $onlyData = $request->only('content', 'status', 'validity', 'group_id', 'type');

        $groupChannel = $this->groupChannelService->firstWhere(['group_id' => $request->input('group_id')]);
        // 若是活動頻則，增加活動頻道位置
        if ($groupChannel->group()->where('public', 1)->exists() && $request->input('type') === NotificationMessageType::JOIN) {
            $content              = json_decode($onlyData['content']);
            $content->isOperating = true;
            $content->link        = '/activity-channel/' . $groupChannel->id;
            $onlyData['content']  = json_encode($content);
        }

        $onlyData['user_id'] = auth()->id();
        try {
            $notificationMessage = $this->notificationMessageService->create($onlyData);

            return $this->setStatus(200)->success($notificationMessage);

        } catch (Exception $exception) {
            return $this->setStatus(404)->fail(['message' => $this->$exception->getMessage()]);

        }


    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse
    {

        $onlyData            = $request->only('content', 'status', 'validity', 'group_id', 'type');
        $onlyData['user_id'] = auth()->id();
        try {
            $notificationInfo = $this->notificationMessageService->update($id, $onlyData);
            return $this->setStatus(201)->success('');
        } catch (Exception $exception) {
            return $this->setStatus(401)->fail(['message' => $exception->getMessage()]);
        }


    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $notificationInfo = $this->notificationMessageService->destroy($id);
            return $this->setStatus(201)->success($notificationInfo);
        } catch (\Exception $exception) {
            return $this->setStatus(401)->fail(['message' => $exception->getMessage()]);
        }
    }
}
