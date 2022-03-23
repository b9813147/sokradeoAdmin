<?php

namespace App\Services\Src;

interface SrcService
{
    public function createSrc($resrcId, $src);
    public function getDetail($src);
    public function getExecuting($src);
}
