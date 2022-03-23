<?php

namespace App\Http\Resources\Api\V1\Score;

use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \Score */
class ScoreCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}
