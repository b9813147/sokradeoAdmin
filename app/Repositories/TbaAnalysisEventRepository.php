<?php

namespace App\Repositories;

use App\Models\TbaAnalysisEvent;
use App\Models\TbaAnalysisEventMode;
use Illuminate\Support\Facades\DB;

class TbaAnalysisEventRepository extends BaseRepository
{
    protected $model;

    public function __construct(TbaAnalysisEvent $tbaAnalysisEvent)
    {
        $this->model = $tbaAnalysisEvent;
    }

    /**
     * @param int $tbaId
     * @param float $observation_offset
     * @param $groups
     * @return bool
     */
    public function createEventGroups(int $tbaId, float $observation_offset, &$groups): bool
    {
        $isSuccessful = true;
        $timestamp    = date("Y-m-d H:i:s");
        try {
            $this->model->query()->where('tba_id', $tbaId)->delete();
            $singlePointEvents = [];
            $events            = [];
            foreach ($groups as $group) {
                $eventMode = TbaAnalysisEventMode::query()->where(['event' => $group['event'], 'mode' => $group['mode']])->first();
                if (is_null($eventMode)) {
                    continue;
                }

                if ($eventMode->type === 0) { // 單點

                    array_push($singlePointEvents, [
                        'tba_id'                     => $tbaId,
                        'tba_analysis_event_mode_id' => $eventMode->id,
                        'time_point'                 => $group['data'] - $observation_offset,
                        'created_at'                 => $timestamp,
                        'updated_at'                 => $timestamp,
                    ]);

                } else { // 區間

                    array_push($events, [
                        'tba_id'                     => $tbaId,
                        'tba_analysis_event_mode_id' => $eventMode->id,
                        'time_start'                 => $group['data'][0] - $observation_offset,
                        'time_end'                   => $group['data'][1] - $observation_offset,
                        'time_point'                 => $group['data'][0] - $observation_offset,
                        'created_at'                 => $timestamp,
                        'updated_at'                 => $timestamp,
                    ]);

                }
            }
            $this->model->query()->insert($events);
            $this->model->query()->insert($singlePointEvents);
        } catch (\Exception $exception) {
            \Log::error('event', ['message' => $exception->getMessage()]);
            $isSuccessful = false;
        }
        return $isSuccessful;
    }

}
