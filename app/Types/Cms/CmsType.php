<?php

namespace App\Types\Cms;

abstract class CmsType
{
    //
    const Video    = 'Video';
    const Tba      = 'Tba';
    const TbaVideo = 'TbaVideo';
    
    //
    public static function check($type)
    {
        switch ($type) {
            case CmsType::Video:
            case CmsType::Tba:
            case CmsType::TbaVideo:
                return true;
            default:
                return false;
        }
    }
    
    //
    public static function checkContent($type)
    {
        switch ($type) {
            case CmsType::Video:
            case CmsType::Tba:
                return true;
            default:
                return false;
        }
    }
    
    //
    public static function list()
    {
        return [
            ['type' => 'Video',    'value' => CmsType::Video,    'text' => '影音'],
            ['type' => 'Tba',      'value' => CmsType::Tba,      'text' => '教學行為分析'],
            ['type' => 'TbaVideo', 'value' => CmsType::TbaVideo, 'text' => '教學行為分析影音'],
        ];
    }
}
