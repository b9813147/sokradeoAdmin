<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/** @see \App\Models\TagType */
class TagTypeCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return $this->collection->map(function ($q) {
            return [
                'id'       => $q->id,
                'content'  => $q->content,
                'order'    => $q->order,
                'group_id' => $q->group_id,
                'tags'     => new TagCollection($q->tags),
            ];
        })->toArray();
    }
}
