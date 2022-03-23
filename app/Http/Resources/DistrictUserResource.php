<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DistrictUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'abbr'          => $this->district->abbr,
            'name'          => $this->user->name,
            'habook'        => $this->user->habook,
            'member_duty'   => $this->member_duty,
            'member_status' => $this->member_status,
            'user_id'       => $this->user_id,
            'districts_id'  => $this->districts_id,
        ];
    }
}
