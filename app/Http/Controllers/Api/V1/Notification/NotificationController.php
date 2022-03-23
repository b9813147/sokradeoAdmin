<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use App\Services\NotificationMessageService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Mockery\Exception;

class NotificationController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;
    /**
     * @var UserService
     */

    /**
     * @var NotificationMessageService
     */
    protected $notificationMessageService;

    /**
     * NotificationController constructor.
     */
    public function __construct(UserService $userService, NotificationMessageService $notificationMessageService)
    {
        $this->userService                = $userService;
        $this->notificationMessageService = $notificationMessageService;
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $event_link   = '/activity-channel/'; // 活動
            $content_link = '/content-review/'; // 評審評分

            $link    = $request->input('isReview') ? $content_link : $event_link;
            $message = [
                'title'          => $request->input('title'),
                'content'        => $request->input('content'),
                'channel_id'     => $request->input('channel_id'),
                'channel_ids'    => $request->input('channel_ids'),
                'team_model_ids' => $request->input('teamModel_ids'),
                'district_ids'   => $request->input('district_ids'),
                'top'            => $request->input('top'),
                'url'            => $request->input('url'),
                'link'           => $link . $request->input('channel_id'),
                'isOperating'    => $request->input('isOperating'),
            ];


            $notificationMessage                = $this->notificationMessageService->create([
                'content'  => json_encode($message),
                'status'   => $request->input('status'),
                'validity' => $request->input('validity'),
                'user_id'  => auth()->id(),
            ]);
            $message['notification_message_id'] = $notificationMessage->id;
            Redis::rpush(getenv('REDIS_NOTIFICATION'), json_encode($message));

        } catch (Exception $exception) {
            return response()->json(['message' => $this->$exception->getMessage(), 404]);
        }

        return response()->json('', 201);

    }
}
