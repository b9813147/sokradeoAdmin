<?php

namespace App\Factories\Src;

use App\Repositories\ConfigRepository;
use App\Services\Src\FileService;
use App\Services\Src\UriService;
use App\Services\Src\Vod\VodService;
use App\Types\Src\SrcType;

class SrcServiceFactory
{
    protected $configRepo;

    /**
     * @var VodService
     */
    protected $vodService;
    /**
     * @var UriService
     */
    protected $uriService;
    /**
     * @var VodServiceFactory
     */
    protected $vodServiceFactory;
    /**
     * @var FileService
     */
    protected $fileService;

    public function __construct(ConfigRepository $configRepo, VodServiceFactory $vodServiceFactory, UriService $uriService, FileService $fileService)
    {
        $this->configRepo        = $configRepo;
        $this->vodServiceFactory = $vodServiceFactory;
        $this->uriService        = $uriService;
        $this->fileService       = $fileService;
    }

    //
    public function create($type, $params = [])
    {
        $srv = null;
        switch ($type) {
            case SrcType::File:
                $srv = $this->fileService;
                break;
            case SrcType::Uri:
                $srv = $this->uriService;
                break;
            case SrcType::Vod:
                if (isset($params['vodType'])) {
                    $srv = $this->vodServiceFactory->createByVodType($params['vodType']);
                } else {
                    $srv = $this->vodServiceFactory->create();
                }
                break;
            default:
                assert(false);
        }
        return $srv;
    }

}
