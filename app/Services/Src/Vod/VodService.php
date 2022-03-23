<?php

namespace App\Services\Src\Vod;

use App\Repositories\VodRepository;
use App\Services\Src\SrcService;
use Exception;

abstract class VodService implements SrcService
{
    protected $vodRepo = null;
    protected $type = null;

    //
    public function __construct(VodRepository $vodRepo)
    {
        $this->vodRepo = $vodRepo;
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

    abstract public function getDetail($src);

    public function getExecuting($src)
    {
        throw new Exception('please implement');
    }

    abstract protected function uploadFile($file); //待確認:若是物件則不須參考& 否則宣告參考&

    abstract protected function toDetail($data);

    abstract protected function toStatus($status);
}
