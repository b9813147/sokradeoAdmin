<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DistrictClassificationByDistrictSubjectCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($districtSubjectInfo) {
            return [
                'text' => $districtSubjectInfo->subject, 'value' => $districtSubjectInfo->id
            ];
        });
    }
}
