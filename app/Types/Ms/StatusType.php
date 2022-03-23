<?php

namespace App\Types\Ms;

abstract class StatusType
{
    //
    const Normal = 'normal';
    
    //
    public static function check($type)
    {
        switch ($type) {
            case StatusType::Normal:
                return true;
            default:
                return false;
        }
    }
    
    //
    public static function list()
    {
        return [
            ['type' => 'Normal', 'value' => StatusType::Normal, 'text' => '正常'],
        ];
    }
}
