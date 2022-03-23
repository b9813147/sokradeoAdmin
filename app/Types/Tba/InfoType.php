<?php

namespace App\Types\Tba;

abstract class InfoType
{
    //
    const AnalEvent            = 'AnalEvent';
    const EvalEvent            = 'EvalEvent';
    const EvalEventBySelf      = 'EvalEvent-Self';
    const EvalEventByTotal     = 'EvalEvent-Total';
    const TechFuns             = 'TechFuns';
    const FreqOfTechUsage      = 'FreqOfTechUsage';
    const AccumTimeOnTechUsage = 'AccumTimeOnTechUsage';
    const TechInteractIdx      = 'TechInteractIdx';
    const MethodAnal           = 'MethodAnal';
    const ContentIdx           = 'ContentIdx';
    
    //
    public static function check($type)
    {
        switch ($type) {
            case InfoType::AnalEvent:
            case InfoType::EvalEvent:
            case InfoType::EvalEventBySelf:
            case InfoType::EvalEventByTotal:
            case InfoType::TechFuns:
            case InfoType::FreqOfTechUsage:
            case InfoType::AccumTimeOnTechUsage:
            case InfoType::TechInteractIdx:
            case InfoType::MethodAnal:
            case InfoType::ContentIdx:
                return true;
            default:
                return false;
        }
    }
    
    //
    public static function list()
    {
        return [
            ['type' => 'AnalEvent',            'value' => InfoType::AnalEvent,            'text' => '行為數據'],
            ['type' => 'EvalEvent',            'value' => InfoType::EvalEvent,            'text' => '評估數據'],
            ['type' => 'EvalEventBySelf',      'value' => InfoType::EvalEventBySelf,      'text' => '個人評估數據'],
            ['type' => 'EvalEventBySelf',      'value' => InfoType::EvalEventByTotal,     'text' => '評估數據總合'],
            ['type' => 'TechFuns',             'value' => InfoType::TechFuns,             'text' => '科技運用'],
            ['type' => 'FreqOfTechUsage',      'value' => InfoType::FreqOfTechUsage,      'text' => '科技運用次數'],
            ['type' => 'AccumTimeOnTechUsage', 'value' => InfoType::AccumTimeOnTechUsage, 'text' => '科技運用時間'],
            ['type' => 'TechInteractIdx',      'value' => InfoType::TechInteractIdx,      'text' => '科技互動指數'],
            ['type' => 'MethodAnal',           'value' => InfoType::MethodAnal,           'text' => '教法應用'],
            ['type' => 'ContentIdx',           'value' => InfoType::ContentIdx,           'text' => '教材實踐'],
        ];
    }
}
