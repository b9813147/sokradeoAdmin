<?php

namespace App\Http\Resources;

use App\Models\GroupChannelContent;
use App\Models\Tba;
use App\Models\GroupChannel;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GlobalRecommendedVideoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($recommendedVideo) {
            return [
                'id'           => $recommendedVideo->id,
                'order'        => $recommendedVideo->order,
                'content_name' => $recommendedVideo->tba instanceof Tba ? $recommendedVideo->tba->name : null,
                'channel_name' => $recommendedVideo->groupChannel instanceof GroupChannel ? $recommendedVideo->groupChannel->name : null,
                'status'       => $recommendedVideo->tba instanceof Tba ? ($recommendedVideo->tba->groupChannelContent instanceof GroupChannelContent ? $recommendedVideo->tba->groupChannelContent->content_status : 0) : 0,
                'public'       => $recommendedVideo->tba instanceof Tba ? ($recommendedVideo->tba->groupChannelContent instanceof GroupChannelContent ? $recommendedVideo->tba->groupChannelContent->content_public : 0) : 0,
            ];
        })->toArray();
    }
}
