<?php

namespace App\Services\Src;

use App\Repositories\UriRepository;
use Illuminate\Support\Facades\Redirect;

class UriService
{
    protected $uriRepo = null;

    //
    public function __construct(UriRepository $uriRepo)
    {
        $this->uriRepo = $uriRepo;
    }

    /**
     * @param $resrcId
     * @param $src
     */
    public function createSrc($resrcId, $src)
    {
        $this->uriRepo->createUri($resrcId, [
            'url'      => $src['url'],
            'duration' => $src['duration']
        ]);
    }

    public function getDetail($src)
    {
        $data = $this->uriRepo->getUriByResourceId($src->resource_id);
        return $this->toDetail($data);
    }

    protected function toDetail($data)
    {
        $list = [];
        array_push($list, [
            'format' => 'mp4',
            'label'  => '1280 X 720',
            'width'  => null,
            'height' => null,
            'size'   => null,
            'url'    => $data->uri->url,
        ]);

        return [
            'duration'  => $data->uri->duration,
            'list'      => $list,
            'status'    => 'ready',
            'thumbnail' => $data->uri->thumbnail,
        ];
    }

    public function getExecuting($src)
    {
        return Redirect::to($src->url);
    }
}
