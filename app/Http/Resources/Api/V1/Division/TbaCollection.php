<?php

namespace App\Http\Resources\Api\V1\Division;

use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \Tba */
class TbaCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'name'        => "$item->alias _ $item->rating_name _ $item->users_name ($item->habook) _ $item->tba_name",
                'content_id'  => $item->id,
                'division_id' => $item->division_id
            ];
        });
    }
}
