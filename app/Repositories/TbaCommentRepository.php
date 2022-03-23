<?php

namespace App\Repositories;

use App\Models\TbaComment;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateUser;
use App\Types\Tba\IdentityType;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 *
 */
class TbaCommentRepository extends BaseRepository
{
    /**
     * @var TbaComment
     */
    protected $model;

    /**
     * @param TbaComment $tbaComment
     */
    public function __construct(TbaComment $tbaComment)
    {
        $this->model = $tbaComment;
    }

    /**
     * @param int $tbaId
     * @param $users
     */
    public function createUsersEventGroups(int $tbaId, &$users)
    {
        foreach ($users as $userInfo) {

            $this->createUserCommentGroups($tbaId, $userInfo);

        }
    }

    //

    /**
     * @param int $tba_id
     * @param $user
     * @return bool
     */
    public function createUserCommentGroups(int $tba_id, &$user): bool
    {

        $isSuccessful = true;
        try {
            $timestamp = date("Y-m-d H:i:s");
            $tag_id    = $user['tag_id'];
            if (is_null($tag_id)) {
                return false;
            }
            $events = [];
            foreach ($user['data'] as $data) {
                $event = [
                    'tag_id'       => $tag_id,
                    'tba_id'       => $tba_id,
                    'user_id'      => $user['user_id'],
                    'comment_type' => 1,
                    'public'       => 1,
                    'time_point'   => $data['time'],
                    'text'         => $data['text'],
                ];
//                if (isset($data['files']) && !empty($data['files'])) {
//                    $this->createEvent($tbaId, $event, $data['files']);
//                    continue;
//                }
//
                $event['created_at'] = $event['updated_at'] = $timestamp;
                array_push($events, $event);
            }
            $this->model->query()->insert($events);
        } catch (\Exception $exception) {
            \Log::info('Create comment', ['message' => $exception->getMessage(), 'status' => 0]);
            $isSuccessful = false;
        }
        return $isSuccessful;
    }

    /**
     * Get all comments based on the given tbaId.
     * @param int $tbaId
     * @param int|null $public - [optional] 1 for public, 0 for private
     * @return EloquentBuilder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getComments(int $tbaId, $public = null)
    {
        // Set up based query
        $comments = $this->getBaseCommentQuery()
            ->where('tba_id', $tbaId);

        // If public is set, filter by public
        if ($public !== null)
            $comments = $comments->where('public', $public);

        // Execute query
        return $comments
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Get the base comment model query
     * @return EloquentBuilder
     */
    private function getBaseCommentQuery(): EloquentBuilder
    {
        return $this->model
            ->with([
                'tag'  => function ($query) {
                    $query->select('id', 'content', 'type_id', 'is_positive');
                    $query->with([
                        'tagType' => function ($query) {
                            $query->select('id', 'content');
                        }
                    ]);
                },
                'tbaCommentAttaches',
                'user' => function ($query) {
                    $query->select('id', 'name', 'habook');
                },
                'tba'  => function ($query) {
                    $query->select('id', 'name', 'teacher', 'habook_id');
                },
            ])
            ->orderBy('updated_at', 'desc');
    }

    /**
     * 新點評轉換到就點評
     * @param int $id
     * @return bool
     */
    public function convertTbaCommentToTbaEvaluateEvent(int $id): bool
    {
        $isSuccessFul = true;
        try {
            $model = $this->model::with([
                'tag' => function ($q) {
                    $q->select('is_positive', 'id');
                }
            ])
                ->select('id', 'tba_id', 'user_id', 'group_id', 'comment_type', 'public', 'time_point', 'tag_id', 'created_at', 'updated_at')
                ->findOrFail($id);
            // 預設訪客
            TbaEvaluateUser::query()->updateOrCreate([
                'tba_id'   => $model->tba_id,
                'identity' => IdentityType::Guest,
                'user_id'  => 702
            ], [
                'tba_id'   => $model->tba_id,
                'identity' => IdentityType::Guest,
                'user_id'  => 702
            ]);
            // Private
            if ($model->public === 0) {
                TbaEvaluateEvent::query()->create([
                    'tba_evaluate_event_mode_id' => $model->tag->is_positive ? 14 : 16,
                    'user_id'                    => $model->user_id,
                    'group_id'                   => $model->group_id,
                    'time_point'                 => $model->time_point,
                    'evaluate_type'              => $model->comment_type,
                    'tba_comment_id'             => $model->id,
                    'tba_id'                     => $model->tba_id,
                ]);
            }
            // E
            if ($model->public === 1 && $model->user_id) {
                $tbaEvaluateUser = TbaEvaluateUser::query()->updateOrCreate([
                    'tba_id'   => $model->tba_id,
                    'identity' => IdentityType::Expert,
                    'user_id'  => $model->user_id
                ], [
                    'tba_id'   => $model->tba_id,
                    'identity' => IdentityType::Expert,
                    'user_id'  => $model->user_id
                ]);

                TbaEvaluateEvent::query()->create([
                    'tba_evaluate_event_mode_id' => $model->tag->is_positive ? 1 : 3,
                    'tba_evaluate_user_id'       => $tbaEvaluateUser->id,
                    'time_point'                 => $model->time_point,
                    'evaluate_type'              => $model->comment_type,
                    'tba_comment_id'             => $model->id,
                    'tba_id'                     => $model->tba_id,
                ]);
            }
            // G
            if ($model->public === 1 && is_null($model->user_id)) {
                TbaEvaluateEvent::query()->create([
                    'tba_evaluate_event_mode_id' => $model->tag->is_positive ? 7 : 9,
                    'tba_evaluate_user_id'       => 702,
                    'time_point'                 => $model->time_point,
                    'evaluate_type'              => $model->comment_type,
                    'tba_comment_id'             => $model->id,
                    'tba_id'                     => $model->tba_id,
                ]);
            }
        } catch (\Exception $exception) {
            \Log::info('comment', ['message' => $exception->getMessage()]);
            $isSuccessFul = false;
        }

        return $isSuccessFul;
    }

    /**
     * 個人點評總數
     *
     * @param int $user_id
     * @return int
     */
    public function personalByPoint(int $user_id): int
    {
        return $this->model->query()
            ->join('group_channel_contents', 'group_channel_contents.content_id', 'tba_comments.tba_id')
            ->distinct('tba_id')
            ->where('public', 1)
            ->where('user_id', $user_id)
            ->count();
    }
}
