<?php

namespace App\Http\Resources;

use App\Models\GroupChannelContent;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RatingCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($rating) {
            return [
                'id'    => $rating->id,
                'type'  => $rating->type,
                'name'  => $rating->name,
                'total' => $rating->groupChannelContent instanceof GroupChannelContent ? $rating->groupChannelContent->total : 0
            ];
        })->toArray();
    }
}
