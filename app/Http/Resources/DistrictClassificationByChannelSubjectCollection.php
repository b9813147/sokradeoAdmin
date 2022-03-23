<?php

namespace App\Http\Resources;

use App\Models\DistrictGroupSubject;
use App\Models\DistrictSubject;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DistrictClassificationByChannelSubjectCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * todo 待修改
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($groupSubjectInfo) {
            return [
                'id'      => $groupSubjectInfo->districtGroupSubject->group_subject_fields_id ?? null,
                'name'    => $groupSubjectInfo->group->name,
                'alias'   => $groupSubjectInfo->alias,
                'subject' => $groupSubjectInfo->districtGroupSubject instanceof DistrictGroupSubject
                    ? $groupSubjectInfo->districtGroupSubject->districtSubject instanceof DistrictSubject
                        ? ['text' => $groupSubjectInfo->districtGroupSubject->districtSubject->subject, 'value' => $groupSubjectInfo->districtGroupSubject->districtSubject->id]
                        : 0
                    : 0,
            ];
        });
    }
}
