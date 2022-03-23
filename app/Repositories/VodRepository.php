<?php

namespace App\Repositories;

use App\Models\Vod;
use Illuminate\Support\Facades\DB;

class VodRepository extends BaseRepository
{
    protected $model;

    /**
     * @param Vod $model
     */
    public function __construct(Vod $model)
    {
        $this->model = $model;
    }

    public function list($page = 1)
    {
        return $this->model->query()->paginate(null, ['*'], 'page', $page);
    }

    //
    public function listByUserId($userId, $page = 1)
    {
        // 待實作
    }

    //
    public function getVod($vodId)
    {
        return $this->model->query()->findOrFail($vodId);
    }

    /**
     * @param int $resourceId
     * @return string
     */
    public function getThumbnailByResourceId(int $resourceId): string
    {
        $tba = DB::table('resources')
            ->join('videos', 'resources.id', 'videos.resource_id')
            ->join('tba_video', 'videos.id', 'tba_video.video_id')
            ->where('resources.id', $resourceId)
            ->get();

        return '//' . $_SERVER['SERVER_NAME'] . '/storage/tba/' . $tba[0]->tba_id . '/' . $tba[0]->thumbnail;
    }

    //
    public function createVod($resrcId, $vod)
    {
        /*
        if ($this->>model->query()->where('resource_id', $resrcId)->exists()) {
            throw new LogicException('resrc of vod is already exist');
        }
        */
        $vod['resource_id'] = $resrcId;
        return $this->model->query()->create($vod);
    }

    //
    public function updateVod($resrcId, $data)
    {
        return $this->model->query()->where('resource_id', '=', $resrcId)->update($data);
    }

}
