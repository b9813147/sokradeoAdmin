<?php

namespace App\Http\Resources\Api\V1\Division;

use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \Division */
class DivisionCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {

        return $this->collection->map(function ($item) {
            $select = ['ratings.name as rating_name', 'group_subject_fields.alias as alias', 'users.name as users_name', 'users.habook', 'tbas.name as tba_name', 'group_channel_contents.division_id', 'tbas.id'];
            $tbas   = $item->tbas()->select($select)
                ->join('users', 'tbas.user_id', 'users.id')
                ->join('group_subject_fields', 'group_channel_contents.group_subject_fields_id', 'group_subject_fields.id')
                ->join('ratings', 'group_channel_contents.ratings_id', 'ratings.id')
                ->orderBy('group_channel_contents.group_subject_fields_id', 'DESC')
                ->orderBy('group_channel_contents.ratings_id', 'DESC')
                ->get();

            return [
                'id'       => $item->id,
                'group_id' => $item->group_id,
                'title'    => $item->title,
                'users'    => new UserCollection($item->users),
                'tbas'     => new TbaCollection($tbas)
            ];
        })->toArray();
    }
}
