<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DistrictResource extends JsonResource
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
            'id'          => $this->id,
            'abbr'        => $this->abbr,
            'school_code' => $this->school_code,
            'status'      => $this->status,
            'thumbnail'   => $this->thumbnail,
            'name'        => $this->districtLang->name,
        ];
    }
}
