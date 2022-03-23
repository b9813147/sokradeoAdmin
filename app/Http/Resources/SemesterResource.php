<?php

namespace App\Http\Resources;

use App\Helpers\Custom\GlobalPlatform;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Semester */
class SemesterResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $result = Carbon::now()->year . '-' . GlobalPlatform::getNearest($this->resource->pluck('month')->toArray(), Carbon::now()->month) . '-' . 0 . 1;

        return [
            'start_date' => $result ?? null,
        ];
    }
}
