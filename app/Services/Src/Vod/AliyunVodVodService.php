<?php

namespace App\Services\Src\Vod;

use App\Models\Vod;
use App\Repositories\VodRepository;
use App\Services\BaseService;
use App\Types\Ms\StatusType;
use App\Types\Src\VodType;

class AliyunVodVodService extends BaseService
{
    protected $vodRepo = null;

    private $params = null;
    private $vodApi = null;

    //
    public function __construct(VodRepository $vodRepo, $params)
    {
        parent::__construct($vodRepo);

        $this->vodRepo = $vodRepo;

        $this->type = VodType::AliyunVod;

        $this->params = $params;

        $this->vodApi = new Vod([
            'regionId'        => $params['RegionId']->val,
            'accessKeyId'     => $params['AccessKeyId']->val,
            'accessKeySecret' => $params['AccessKeySecret']->val,
            'cateId'          => $params['CategoryId']->val,
        ]);
    }

    public function getDetail($src)
    {
        if (is_null($src->rdata)) {
            $data = $this->vodApi->getPlayInfo($src->rid);
            $this->vodRepo->updateVod($src->resource_id, ['rdata' => json_encode($data, JSON_UNESCAPED_UNICODE)]);
        } else {
            $data = json_decode($src->rdata);
        }
        return $this->toDetail($data);
    }

    protected function uploadFile($file)
    {
        // 待實作
    }

    protected function toDetail($data)
    {
        $list     = [];
        $playList = $data->PlayInfoList->PlayInfo;
        foreach ($playList as $playInfo) {
            array_push($list, [
                'format' => $playInfo->Format,
                'label'  => $playInfo->Width . ' X ' . $playInfo->Height,
                'width'  => $playInfo->Width,
                'height' => $playInfo->Height,
                'size'   => 0,
                'url'    => $playInfo->PlayURL,
            ]);
        }

        return [
            'duration'  => 0,
            'list'      => $list,
            'status'    => $this->toStatus($data->VideoBase->Status),
            'thumbnail' => $data->VideoBase->CoverURL,
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
