<?php

namespace App\Http\Resources;

use App\Models\DistrictGroupSubject;
use App\Models\DistrictSubject;
use App\Models\Grade;
use App\Models\GradeLang;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DistrictChannelContentCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($districtChannelContentInfo) {
            return [
                'id'         => $districtChannelContentInfo->id,
                'content_id' => $districtChannelContentInfo->content_id,
                'name'       => $districtChannelContentInfo->tba->name,
                'district'   => $districtChannelContentInfo->district->abbr,
                'rating'     => ['text' => $districtChannelContentInfo->rating->name, 'value' => $districtChannelContentInfo->rating->id],
                'subject'    => $districtChannelContentInfo->districtGroupSubject instanceof DistrictGroupSubject
                    ? $districtChannelContentInfo->districtGroupSubject->districtSubject instanceof DistrictSubject
                        ? ['text' => $districtChannelContentInfo->districtGroupSubject->districtSubject->alias, 'value' => $districtChannelContentInfo->districtGroupSubject->districtSubject->id]
                        : null
                    : null,
                'grade'      => $districtChannelContentInfo->grade instanceof Grade
                    ? $districtChannelContentInfo->grade->gradeLang instanceof GradeLang
                        ? ['text' => $districtChannelContentInfo->grade->gradeLang->name, 'value' => $districtChannelContentInfo->grade->gradeLang->id]
                        : null
                    : null,

            ];
        });
    }
}
