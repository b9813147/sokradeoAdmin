<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Group
 * @property array tagTypes
 * @property array notificationMessages
 */
class GroupResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'school_code'        => $this->school_code,
            'name'               => $this->name,
            'description'        => $this->description,
            'thumbnail'          => $this->thumbnail,
            'status'             => $this->status,
            'notify_status'      => $this->notify_status,
            'public'             => $this->public,
            'review_status'      => (int)$this->review_status,
            'public_note_status' => $this->public_note_status,
//            'created_at'         => $this->created_at,
//            'updated_at'         => $this->updated_at,
            'users_count'        => $this->users_count,
            'users'              => $this->users->map(function ($q) {
                return [
                    "habook"     => $q->habook,
                    "name"       => $q->name,
                    "pivot"      => [
                        "group_id"      => $q->pivot->group_id,
                        "user_id"       => $q->pivot->user_id,
                        "member_status" => $q->pivot->member_status,
                        "member_duty"   => $q->pivot->member_duty,
                    ],
                    "created_at" => $q->pivot->created_at->toDateTimeString(),
                ];
            })->sortByDesc('created_at')->all(),
            'notifications'      => $this->notificationMessages,
            'tagTypes'           => new TagTypeCollection($this->tagTypes),
//            'channels' => new GroupChannelResource($this->whenLoaded('channels')),
        ];
    }
}
