<?php

namespace App\Types\Src;

abstract class SrcType
{
    //
    const File = 'File';
    const Uri  = 'Uri';
    const Vod  = 'Vod';
    
    //
    public static function check($type)
    {
        switch ($type) {
            case SrcType::File:
            case SrcType::Uri:
            case SrcType::Vod:
                return true;
            default:
                return false;
        }
    }
    
    //
    public static function list()
    {
        return [
            ['type' => 'File', 'value' => SrcType::File, 'text' => '檔案'],
            ['type' => 'Uri',  'value' => SrcType::Uri,  'text' => '連結'],
            ['type' => 'Vod',  'value' => SrcType::Vod,  'text' => '串流'],
        ];
    }
}
