<?php

namespace Tests\Feature;

use App\Helpers\Custom\GlobalPlatform;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\Tba;
use App\Models\TbaAnnex;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateUser;
use App\Models\TbaStatistic;
use App\Models\User;
use App\Types\Tba\AnnexType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Exception;
use Tests\TestCase;

class TbaTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $tbaId = [14, 39];
        $tba   = Tba::query()
            ->with([
                'tbaStatistics' => function ($q) {
                    $q->whereBetween('type', [55, 60])->select('tba_id', 'type', 'idx');
                }
            ])
            ->select('id', 'name', 'thumbnail', 'subject', 'grade', 'educational_stage_id', 'description', 'teacher')
            ->selectRaw("created_at date")
            ->whereIn('id', $tbaId)
            ->orderBy('created_at', 'DESC')
            ->get();

//        dd($tba->toArray());

        $this->assertTrue(true);
    }

    public function testRun()
    {
        $selectSql = "tbas.habook_id, users.id user_id,users.name,group_concat(tbas.id) tba_ids,tbas.lecture_date,
               case
                   when group_channel_contents.content_public = 1 and group_channel_contents.content_status = 1
                       then count(group_channel_contents.content_id)
                   else 0
                   end as public_video,
               case
                   when group_channel_contents.content_public = 1 or group_channel_contents.content_public = 0 or
                        group_channel_contents.content_status = 1 or group_channel_contents.content_status = 2
                       then count(group_channel_contents.content_id)
                   else 0
                   end as video_total,
               case
                   when group_channel_contents.content_public = 1 or group_channel_contents.content_public = 0 or
                        group_channel_contents.content_status = 1 or group_channel_contents.content_status = 2
                       then sum(hits)
                   else 0
                   end as his_total
          ";

        $date       = [];
        $search     = null;
        $channel_id = GlobalPlatform::convertGroupIdToChannelId(3);
        $tba        = Tba::query();

        if ($date) {
            $tba->whereBetween('lecture_date', $date);
        }

        $column = null;
        if ($search) {
            $tba->where($column, 'like', "$search%");
        }

        $result = $tba->selectRaw($selectSql)
            ->join('group_channel_contents', 'tbas.id', 'group_channel_contents.content_id')
            ->join('users', 'tbas.habook_id', 'users.habook')
            ->where('group_channel_contents.group_channel_id', $channel_id)
            ->whereNotNull('tbas.habook_id')
            ->groupBy('tbas.habook_id')
            ->paginate(15);

        $tbaEvaluateEvent = TbaEvaluateEvent::query();
        $tbaEvaluateUser  = TbaEvaluateUser::query()->join('tba_evaluate_events as tee', 'tee.tba_evaluate_user_id', 'tba_evaluate_users.id');
        $result->each(function ($q) use ($tbaEvaluateEvent, $tbaEvaluateUser) {
            $tba_ids             = explode(',', $q->tba_ids);
            $q->tbaAnnex         = $this->tbaAnnex($tba_ids);
            $q->doubleGreenLight = $this->tbaStatistics($tba_ids);
            $q->private_mark     = $tbaEvaluateEvent->where('user_id', $q->user_id)->get()->count();
            $q->total_mark       = $tbaEvaluateUser->where('tba_evaluate_users.identity', '!=', 'G')->whereIn('tba_evaluate_users.tba_id', $tba_ids)->get()->count();
            $q->public_mark      = $tbaEvaluateUser->where('tba_evaluate_users.user_id', $q->user_id)->get()->count();
        });


        $this->assertTrue(true);
    }

    protected function tbaStatistics($tba_ids)
    {
        $tbaStatistics = TbaStatistic::query()->selectRaw("
        MAX(CASE WHEN type = 47 THEN CAST(idx AS signed) ELSE 0 END) AS T,
        MAX(CASE WHEN type = 48 THEN CAST(idx AS signed) ELSE 0 END) AS P,tba_id
        ")->whereIn('tba_id', $tba_ids)->groupBy('tba_id')->get();

        $result = 0;
        try {
            $tbaStatistics->each(function ($q) use (&$result) {
                if ($q->T >= 70 && $q->P >= 70) {
                    $result++;
                }
            });
            return $result;
        } catch (Exception $exception) {
            return $result;
        }
    }

    protected function tbaAnnex($tba_ids)
    {
        TbaAnnex::query()->selectRaw(`
        case
           when type = 'Material'
               then count(tba_id)
           else 0
           end as material,
       case
           when type = 'LessonPlan'
               then count(tba_id)
           else 0
           end as lessonPlan`)->whereIn('tba_id', $tba_ids)->get()->first()->toArray();
    }

    public function testDate()
    {
        $carbon  = Carbon::now();
        $current = $carbon->format('Y-m-d');

        dd($carbon->sub(2, 'day')->format('Y-m-d'), $current);
    }

}
