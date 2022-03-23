<?php

namespace App\Types\Tba;

use App\Types\Src\SrcType;

abstract class AnnexType
{
    //
    const HiTeachNote = 'HiTeachNote';
    const LessonPlan  = 'LessonPlan';
    const Material    = 'Material';
    const Link        = 'Link';
    const Other       = 'Other';
    
    //
    public static function check($type)
    {
        switch ($type) {
            case AnnexType::HiTeachNote:
            case AnnexType::LessonPlan:
            case AnnexType::Material:
            case AnnexType::Link:
            case AnnexType::Other:
                return true;
            default:
                return false;
        }
    }
    
    //
    public static function list()
    {
        return [
            ['type' => 'HiTeachNote', 'value' => AnnexType::HiTeachNote, 'text' => '電子筆記'],
            ['type' => 'LessonPlan',  'value' => AnnexType::LessonPlan,  'text' => '教案'],
            ['type' => 'Material',    'value' => AnnexType::Material,    'text' => '教材'],
            ['type' => 'Link',        'value' => AnnexType::Link,        'text' => '連結'],
            ['type' => 'Other',       'value' => AnnexType::Other,       'text' => '其他'],
        ];
    }
    
    //
    public static function getSrcType($type)
    {
        $srcType = null;
        switch ($type) {
            case AnnexType::HiTeachNote:
            case AnnexType::LessonPlan:
            case AnnexType::Material:
            case AnnexType::Other:
                $srcType = SrcType::File;
                break;
            case AnnexType::Link:
                $srcType = SrcType::Uri;
                break;
            default:
                return false;
        }
        return $srcType;
    }
    
}
