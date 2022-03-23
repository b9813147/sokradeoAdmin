<?php

namespace App\Http\Resources\Api\V1\Division;

use Illuminate\Http\Resources\Json\JsonResource;


/** @see \Group */
class GroupResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        dd($request->all());

//        return [
//            'id'                 => $this->id,
//            'school_code'        => $this->school_code,
//            'name'               => $this->name,
//            'description'        => $this->description,
//            'thumbnail'          => $this->thumbnail,
//            'status'             => $this->status,
//            'public'             => $this->public,
//            'review_status'      => $this->review_status,
//            'public_note_status' => $this->public_note_status,
//            'created_at'         => $this->created_at,
//            'updated_at'         => $this->updated_at,
//            'users_count'        => $this->users_count,
//
//            'channels' => new GroupChannelResource($this->whenLoaded('channels')),
//            'users'    => UserCollection::collection($this->whenLoaded('users')),
//        ];
    }
}
