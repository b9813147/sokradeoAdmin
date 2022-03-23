<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DistrictUserCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($districtUsers) {
            return [
                'abbr'          => $districtUsers->districtLang->name,
                'name'          => $districtUsers->user->name,
                'habook'        => $districtUsers->user->habook,
                'member_duty'   => $districtUsers->member_duty,
                'member_status' => $districtUsers->member_status,
                'user_id'       => $districtUsers->user_id,
                'districts_id'  => $districtUsers->districts_id,
            ];
        })->toArray();
    }
}
