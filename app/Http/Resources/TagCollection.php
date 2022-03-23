<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/** @see \App\Models\Tag */
class TagCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return $this->collection->map(function ($q) {
            return [
                'id'      => $q->id,
                'content' => $q->content,
                'type_id' => $q->type_id
            ];
        })->toArray();

    }
}
