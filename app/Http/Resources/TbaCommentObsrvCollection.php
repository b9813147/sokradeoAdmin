<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\TbaCommentaComment */
class TbaCommentObsrvCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        // Observer comments sorted by time ASC
        $observerComments = $this->collection->map(function ($item) {
            $observerName = empty($item['user']) ? $item['nick_name'] : $item['user']['name'];
            $habook       = empty($item['user']) ? null : $item['user']['habook'];
            return [
                'attachment' => $item['attachment'],
                'tag'        => $item['tag'],
                'text'       => $item['text'],
                'time'       => $item['time'],
                'type'       => $item['type'],
                'isPositive' => $item['is_positive'],
                'name'       => $observerName,
                'habook'     => $habook,
            ];
        })->sortBy('time')->toArray();

        // Observer Info List (userId, name, habook, total comments)
        $observers = $this->collection->map(function ($item) use ($observerComments) {
            $userId       = empty($item['user']) ? null : $item['user']['id'];
            $observerName = empty($item['user']) ? $item['nick_name'] : $item['user']['name'];
            $habook       = empty($item['user']) ? null : $item['user']['habook'];
            // Total comments for each observer
            $totalComments = count(array_filter(
                $observerComments,
                function ($observerCommentsData) use ($observerName, $habook) {
                    if (empty($observerCommentsData['habook']))
                        return $observerCommentsData['name'] == $observerName;
                    return $observerCommentsData['habook'] == $habook;
                }
            ));
            return [
                'userId'        => $userId,
                'name'          => $observerName,
                'habook'        => $habook,
                'totalComments' => $totalComments,
            ];
        })->unique('name')->sortByDesc('totalComments')->toArray();

        // Observer Comment Tags
        $observerCommentTags = $this->collection->map(function ($item) use ($observerComments) {
            if ($item['tag']) {
                $tagName   = $item['tag']['name'];
                $totalTags = count(array_filter(
                    $observerComments,
                    function ($observerCommentsData) use ($tagName) {
                        return $observerCommentsData['tag']['name'] == $tagName;
                    }
                ));
                return [
                    'tag'        => $item['tag'],
                    'totalTags'  => $totalTags,
                    'type'       => $item['type'],
                    'isPositive' => $item['is_positive'],
                ];
            }
        })->unique('tag')->sortByDesc('totalTags')->toArray();

        $commentInfo = [
            'observerComments'    => $observerComments,
            'observers'           => $observers,
            'observerCommentTags' => $observerCommentTags,
        ];
        return $commentInfo;
    }
}
