<?php

namespace App\Repositories;

use App\Models\Tba;
use App\Models\GroupChannel;
use App\Models\GroupChannelContent;
use App\Models\RecommendedVideo;
use App\Types\Cms\CmsType;

class RecommendedVideoRepository extends BaseRepository
{
    protected $model;

    /**
     * RecommendedVideoRepository constructor.
     * @param $model
     */
    public function __construct(RecommendedVideo $model)
    {
        $this->model = $model;
    }

    //
    public function getWithLimit($limit = null)
    {
//        $recommendedVideos = $this->model->query()->with(['tba', 'groupChannel'])->limit($limit)->orderBy('order')->get()->map(function ($q) {
//            $q->group_channel_content = GroupChannelContent::query()->where(['group_channel_id' => $q->group_channel_id, 'content_id' => $q->tba_id])->first();
//            return $q;
//        });

        $query             = $this->model->query()->limit($limit)->orderBy('order');
        $group_channel_ids = $query->pluck('group_channel_id');
        $recommendedVideos = $query->with([
            'groupChannel', 'tba' => function ($q) use ($group_channel_ids) {
                $q->with([
                    'groupChannelContent' => function ($q) use ($group_channel_ids) {
                        $q->whereIn('group_channel_id', $group_channel_ids);
                    }
                ]);
            }
        ])->limit($limit)->orderBy('order')->get();

        return $recommendedVideos;
    }
}
