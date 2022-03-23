<?php

namespace App\Services;

use App\Repositories\RecommendedVideoRepository;
use Yish\Generators\Foundation\Service\Service;

class RecommendedVideoService extends BaseService
{
    protected $repository;

    /**
     * RecommendedVideoService constructor.
     * @param $repository
     */
    public function __construct(RecommendedVideoRepository $repository)
    {
        $this->repository = $repository;
    }

    //
    public function getWithLimit($limit = null)
    {
        $recommendedVideos = $this->repository->getWithLimit($limit);
        return $recommendedVideos;
    }

    //
    public function getNotIncludedChannelContent()
    {

    }
}
