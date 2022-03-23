<?php

namespace App\Http\Resources\Api\V1\Division;

use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\User */
class UserCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($user) {
            return [
                'id'   => $user->id,
                'name' => $user->name . ' (' . $user->habook . ')',
            ];
        });
    }
}
