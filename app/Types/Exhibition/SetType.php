<?php

namespace App\Types\Exhibition;

abstract class SetType
{
    //
    const Excellent = 'Excellent';
    const Top       = 'Top';
    
    //
    public static function check($type)
    {
        switch ($type) {
            case SetType::Excellent:
            case SetType::Top:
                return true;
            default:
                return false;
        }
    }
    
    //
    public static function list()
    {
        return [
            ['type' => 'Excellent', 'value' => SetType::Excellent, 'text' => '精選'],
            ['type' => 'Top',       'value' => SetType::Top,       'text' => '置頂'],
        ];
    }
}
