<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\Districts */
class DistrictsCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($v) {
            return [
                'id'          => $v->id,
                'abbr'        => $v->abbr,
                'school_code' => $v->school_code,
                'thumbnail'   => $v->thumbnail,
                'status'      => $v->status,
                'name'        => $v->districtLang->name,
                'description' => $v->districtLang->description,
                'group_ids'   => $v->groups()->get()->pluck('id'),
            ];
        });
    }
}
