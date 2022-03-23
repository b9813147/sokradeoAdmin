<?php

namespace App\Types\Src;

abstract class VodType
{
    //
    const Msr = 'Msr';
    const AliyunVod = 'AliyunVod';
    const AzureFile = 'AzureFile';


    //
    public static function check($type)
    {
        switch ($type) {
            case VodType::Msr:
            case VodType::AliyunVod:
            case VodType::AzureFile:
                return true;
            default:
                return false;
        }
    }

    //
    public static function list()
    {
        return [
            ['type' => 'Msr', 'value' => VodType::Msr, 'text' => '愛錄客'],
            ['type' => 'AliyunVod', 'value' => VodType::AliyunVod, 'text' => '阿里雲'],
            ['type' => 'AliyunVod', 'value' => VodType::AzureFile, 'text' => 'Azure'],
        ];
    }
}
