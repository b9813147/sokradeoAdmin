<?php

namespace App\Types\Exhibition;

abstract class OrderType
{
    //
    const Hits               = 'Hits';
    const AddedTime          = 'AddedTime';
    const TbaTechInteractIdx = 'TbaTechInteractIdx';
    const TbaMethodAnal      = 'TbaMethodAnal';
    
    //
    public static function check($type)
    {
        switch ($type) {
            case OrderType::Hits:
            case OrderType::AddedTime:
            case OrderType::TbaTechInteractIdx:
            case OrderType::TbaMethodAnal:
                return true;
            default:
                return false;
        }
    }
    
    //
    public static function list()
    {
        return [
            ['type' => 'Hits',               'value' => OrderType::Hits,               'text' => '點閱率'],
            ['type' => 'AddedTime',          'value' => OrderType::AddedTime,          'text' => '上架時間'],
            ['type' => 'TbaTechInteractIdx', 'value' => OrderType::TbaTechInteractIdx, 'text' => '教學行為分析科技互動指數'],
            ['type' => 'TbaMethodAnal',      'value' => OrderType::TbaMethodAnal,      'text' => '教學行為分析教法應用'],
        ];
    }
}
