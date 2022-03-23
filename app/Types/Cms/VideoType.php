<?php

namespace App\Types\Cms;

use App\Types\Src\SrcType;

abstract class VideoType
{
    //
    const Vod = SrcType::Vod;
    
    //
    public static function check($type)
    {
        switch ($type) {
            case VideoType::Vod:
                return true;
            default:
                return false;
        }
    }
    
    //
    public static function list()
    {
        return [
            ['type' => 'Vod', 'value' => VideoType::Vod, 'text' => '串流'],
        ];
    }
}
