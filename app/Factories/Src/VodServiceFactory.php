<?php

namespace App\Factories\Src;

use App\Services\Src\Vod\AzureFileVodService;
use App\Services\Src\Vod\MsrVodService;
use App\Repositories\ConfigRepository;
use App\Repositories\VodRepository;
use App\Services\Src\Vod\AliyunVodVodService;
use App\Types\Src\SrcType;
use App\Types\Src\VodType;

class VodServiceFactory
{
    private $configRepo = null;
    private $vodRepo = null;
    private $vodParams = null;

    //
    public function __construct(ConfigRepository $configRepo, VodRepository $vodRepo)
    {
        $this->configRepo = $configRepo;
        $this->vodRepo    = $vodRepo;
        $this->vodParams  = $configRepo->getParamsByCate(SrcType::Vod);
    }

    //
    public function create()
    {
        $type = $this->vodParams['Type']->val;

        $srv = null;
        switch ($type) {
            case VodType::Msr:
                $params = $this->configRepo->getParamsByCate(VodType::Msr);
                $srv    = new MsrVodService($this->vodRepo, $params);
                break;
            case VodType::AliyunVod:
                $params = $this->configRepo->getParamsByCate(VodType::AliyunVod);
                $srv    = new AliyunVodVodService($this->vodRepo, $params);
                break;
            case VodType::AzureFile:
                $params = $this->configRepo->getParamsByCate(VodType::AzureFile);
                $srv    = new AzureFileVodService($this->vodRepo);
                break;
            default:
                assert(false);
        }
        return $srv;
    }

    //
    public function createByVodType($vodType)
    {
        $srv = null;
        switch ($vodType) {
            case VodType::Msr:
                $params = $this->configRepo->getParamsByCate(VodType::Msr);
                $srv    = new MsrVodService($this->vodRepo, $params);
                break;
            case VodType::AliyunVod:
                $params = $this->configRepo->getParamsByCate(VodType::AliyunVod);
                $srv    = new AliyunVodVodService($this->vodRepo, $params);
                break;
            case VodType::AzureFile:
                $params = $this->configRepo->getParamsByCate(VodType::AzureFile);
                $srv    = new AzureFileVodService($this->vodRepo);
                break;
            default:
                assert(false);
        }
        return $srv;

    }

    public function createSrc($resrcId, $src)
    {
        if (isset($src['file']) && !is_null($src['file'])) { // 待處理檔案上傳方式

            $vod = $this->uploadFile($src['file']);
            // $src = ...待更新src
        }

        $src['type'] = $this->type;

        return $this->vodRepo->createVod($resrcId, $src);
    }
}
