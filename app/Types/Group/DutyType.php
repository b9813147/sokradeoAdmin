<?php

namespace App\Types\Group;

abstract class DutyType
{
    //
    const General = 'General';
    const Admin   = 'Admin';
    const Manager = 'Manager';
    const Expert  = 'Expert';
    
    //
    public static function check($type)
    {
        switch ($type) {
            case DutyType::General:
            case DutyType::Admin:
            case DutyType::Manager:
            case DutyType::Expert:
                return true;
            default:
                return false;
        }
    }
    
    //
    public static function list()
    {
        return [
            ['type' => 'General', 'value' => DutyType::General, 'text' => '一般用戶'],
            ['type' => 'Admin',   'value' => DutyType::Admin  , 'text' => '管理者'],
            ['type' => 'Manager', 'value' => DutyType::Manager, 'text' => '維護者'],
            ['type' => 'Expert',  'value' => DutyType::Expert,  'text' => '專家'],
        ];
    }
    
    //
    public static function checkManagement($type)
    {
        switch ($type) {
            case DutyType::Admin:
            case DutyType::Manager:
                return true;
            default:
                return false;
        }
    }
}
