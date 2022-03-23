<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\SubjectField */
class SubjectFieldCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->flatMap(function ($subjectField) use ($request) {
            return $subjectField->subjectLang->map(function ($subjectLang) {
                return [
                    'id'   => $subjectLang->subject_fields_id,
                    'type' => $subjectLang->name
                ];
            })->toArray();
        })->toArray();

    }
}
