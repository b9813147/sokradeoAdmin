<?php

namespace App\Http\Resources;

use App\Models\GroupChannelContent;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\GroupSubjectField */
class GroupSubjectFieldCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($groupSubjectField) {
            return [
                'id'                => $groupSubjectField->id,
                'subject'           => $groupSubjectField->subject,
                'alias'             => $groupSubjectField->alias,
                'subject_fields_id' => $groupSubjectField->subjectFields->isNotEmpty() ? $groupSubjectField->subjectFields->first()->id : null,
                'order'             => $groupSubjectField->order,
                'total' => $groupSubjectField->groupChannelContent instanceof GroupChannelContent ? $groupSubjectField->groupChannelContent->total : 0

            ];
        })->toArray();

    }
}
