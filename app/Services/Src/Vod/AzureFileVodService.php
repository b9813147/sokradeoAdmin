<?php

namespace App\Services\Src\Vod;

use App\Libraries\Azure\Blob;
use App\Repositories\VodRepository;
use App\Services\Src\Vod\BaseService;
use App\Types\Ms\StatusType;
use App\Types\Src\VodType;

class AzureFileVodService extends BaseService
{
    protected $vodRepo = null;

    private $vodApi = null;

    //
    public function __construct(VodRepository $vodRepo)
    {
        parent::__construct($vodRepo);

        $this->vodRepo = $vodRepo;

        $this->type = VodType::AzureFile;
    }

    public function getDetail($src)
    {
        $data              = json_decode($src->rdata);
        $data->resource_id = $src->resource_id;
        return $this->toDetail($data);
    }

    protected function uploadFile($file)
    {
        // 待實作
    }

    protected function toDetail($data)
    {
        $blob_config = \config('Azure.blob.' . $data->source);

        $blob = new Blob($blob_config['account'], $blob_config['key'], $blob_config['endpoint']);
        $url  = $blob->GetUrlByToken($data->blob, $data->container);
        $list = [];
        array_push($list, [
            'format' => 'mp4',
            'label'  => '1280 X 720',
            'width'  => null,
            'height' => null,
            'size'   => null,
            'url'    => $url['url'],
        ]);

        return [
            'duration'  => 0,
            'list'      => $list,
            'status'    => StatusType::Normal,
            'thumbnail' => $this->vodRepo->getThumbnailByResourceId($data->resource_id),
        ];
    }

    protected function toStatus($status)
    {
        switch ($status) {
            case 'Normal':
                $status = StatusType::Normal;
                break;
            default:
                return null;
        }
        return $status;
    }

}
