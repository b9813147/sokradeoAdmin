<?php

namespace App\Console\Commands;

use App\Enums\Observation\ClassStatus;
use App\Services\App\ObservationService;
use App\Services\ObservationClassService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateTbaVideoCommand extends Command
{
    protected $signature = 'video:create';

    protected $description = '檢查App 同步譯課結束並上傳影片';

    protected $observationClassService;
    protected $observationService;

    /**
     * @param ObservationClassService $observationClassService
     * @param ObservationService $observationService
     */
    public function __construct(ObservationClassService $observationClassService, ObservationService $observationService)
    {
        parent::__construct();
        $this->observationClassService = $observationClassService;
        $this->observationService      = $observationService;
    }

    public function handle()
    {
        // Current Time
        $currentTime = Carbon::now()->timestamp;
        // Find the course status as started
        $this->observationClassService->findWhere(['status' => ClassStatus::START])->each(function ($q) use ($currentTime) {
            // Calculate expiration time
            $classTime = ($q->duration) + $q->timestamp;
            if ($currentTime >= $classTime) {
                if ($this->observationService->createLesson($q->id)) {
                    $q->status = ClassStatus::END;
                    $q->save();
                }
            }
        });
    }
}
