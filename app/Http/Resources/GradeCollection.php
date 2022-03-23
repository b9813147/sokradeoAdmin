<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GradeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $collection = $this->collection->map(function ($grade) {
            return [
                'name' => $grade->name,
                'id'   => $grade->grades_id,
            ];
        });
        $collection->push(
            [
                'name' => __('grade.other'),
                'id'   => 'Other',
            ]
        );
        return $collection->toArray();
    }
}
