<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DistrictSubjectCollection extends ResourceCollection
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
            $total = 0;
            // 課例數
            $districtSubjectInfo->districtGroupSubject->map(function ($q) use (&$total) {
                return $total += $q->groupSubjectField->district_channel_content_count;
            });

            return [
                'id'      => $districtSubjectInfo->id,
                'subject' => $districtSubjectInfo->subject,
                'alias'   => $districtSubjectInfo->districtGroupSubject->map(function ($q) {
                    return [
                        'text'  => $q->groupSubjectField->alias ?? null,
                        'name'  => $q->groupSubjectField->group->name ?? null,
                        'value' => $q->groupSubjectField->id ?? null
                    ];
                }),
                'order'   => $districtSubjectInfo->order,
                'total'   => $total
            ];
        })->toArray();
    }
}
