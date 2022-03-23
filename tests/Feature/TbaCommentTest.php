<?php

namespace Tests\Feature;

use App\Models\TbaComment;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateUser;
use App\Types\Tba\IdentityType;
use Tests\TestCase;

class TbaCommentTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_convert_to_tba_evaluate_event($id)
    {
        TbaComment::with([
            'tag' => function ($q) {
                $q->select('is_positive', 'id');
            }
        ])->select('id', 'tba_id', 'user_id', 'group_id', 'comment_type', 'public', 'time_point', 'tag_id', 'created_at', 'updated_at')->findOrFail($id)->each(function ($q) {
            // Private
            if ($q->public === 0) {
                TbaEvaluateEvent::query()->create([
                    'tba_id'                     => $q->tba_id,
                    'tba_evaluate_event_mode_id' => $q->tag->is_positive ? 14 : 16,
                    'user_id'                    => $q->user_id,
                    'group_id'                   => $q->group_id,
                    'time_point'                 => $q->time_ponint,
                    'evaluate_type'              => $q->comment_type,
                    'tba_comment_id'             => $q->id,
                    'created_at'                 => $q->created_at,
                    'updated_at'                 => $q->updated_at
                ]);
            }
            // E
            if ($q->public === 1 && $q->user_id) {
                $tbaEvaluateUser = TbaEvaluateUser::query()->updateOrCreate([
                    'tba_id'   => $q->tba_id,
                    'identity' => IdentityType::Expert,
                    'user_id'  => $q->user_id
                ], [
                    'tba_id'   => $q->tba_id,
                    'identity' => IdentityType::Expert,
                    'user_id'  => $q->user_id
                ]);
                TbaEvaluateEvent::query()->create([
                    'tba_id'                     => $q->tba_id,
                    'tba_evaluate_event_mode_id' => $q->tag->is_positive ? 1 : 3,
                    'tba_evaluate_event_user_id' => $tbaEvaluateUser->id,
                    'time_point'                 => $q->time_ponint,
                    'evaluate_type'              => $q->comment_type,
                    'tba_comment_id'             => $q->id,
                    'created_at'                 => $q->created_at,
                    'updated_at'                 => $q->updated_at
                ]);
            }
            // G
            if ($q->public === 1 && is_null($q->user_id)) {
                $tbaEvaluateUser = TbaEvaluateUser::query()->updateOrCreate([
                    'tba_id'   => $q->tba_id,
                    'identity' => IdentityType::Expert,
                    'user_id'  => $q->user_id
                ], [
                    'tba_id'   => $q->tba_id,
                    'identity' => IdentityType::Expert,
                    'user_id'  => $q->user_id
                ]);
                TbaEvaluateEvent::query()->create([
                    'tba_id'                     => $q->tba_id,
                    'tba_evaluate_event_mode_id' => $q->tag->is_positive ? 7 : 9,
                    'tba_evaluate_event_user_id' => $tbaEvaluateUser->id,
                    'time_point'                 => $q->time_ponint,
                    'evaluate_type'              => $q->comment_type,
                    'tba_comment_id'             => $q->id,
                    'created_at'                 => $q->created_at,
                    'updated_at'                 => $q->updated_at
                ]);
            }
        });
    }
}
