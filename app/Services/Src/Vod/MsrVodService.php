<?php

namespace App\Services\Src\Vod;

use App\Repositories\VodRepository;
use App\Services\BaseService;
use App\Types\Ms\StatusType;
use App\Types\Src\VodType;

class MsrVodService extends BaseService
{
    protected $vodRepo = null;

    private $params = null;
    private $vodApi = null;

    //
    public function __construct(VodRepository $vodRepo, $params)
    {
        parent::__construct($vodRepo);

        $this->vodRepo = $vodRepo;

        $this->type = VodType::Msr;

        $this->params = $params;

        $this->vodApi = new Vod([
            'protocol' => $params['Protocol']->val,
            'dn'       => $params['Dn']->val,
            'cateId'   => '', // 待實作
        ]);
    }

    public function getDetail($src)
    {
//        $data = $this->vodApi->getVideo($src->rid);

        if (is_null($src->rdata)) {
            $data = $this->vodApi->getVideo($src->rid);
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
        $channel  = $data->channels[0];
        $list     = [
            [
                'format' => 'm3u8',
                'label'  => 'Auto',
                'size'   => $channel->size,
                'url'    => $channel->hls,
            ]
        ];
        $playList = $channel->videoStreams;
        foreach ($playList as $playInfo) {
            array_push($list, [
                'format' => 'm3u8',
                'label'  => $playInfo->width . ' X ' . $playInfo->height,
                'width'  => $playInfo->width,
                'height' => $playInfo->height,
                'size'   => $playInfo->size,
                'url'    => $playInfo->hls,
            ]);
        }

        return [
            'duration'  => $data->duration,
            'list'      => $list,
            'status'    => $this->toStatus($data->status),
            'thumbnail' => $channel->preview,
        ];
    }

    protected function toStatus($status)
    {
        switch ($status) {
            case 'ready':
                $status = StatusType::Normal;
                break;
            default:
                return null;
        }
        return $status;
    }
}
