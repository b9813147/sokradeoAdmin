<?php

namespace App\Types\App;

/*
 * 角色類型:對映資料庫表格roles的預設角色(預設角色是資料庫資料填充的初始資料, 後續自行客製新增不在此限)
 * 用途目的:配合應用程式邏輯需使用全站式角色而增設
 * */

abstract class RoleType
{
    //
    const Root    = 'Root';
    const Admin   = 'Admin';
    const Manager = 'Manager';
    const Test    = 'Test';
    const Expert  = 'Expert';
    const Teacher = 'Teacher';
    const Parent  = 'Parent';
    const Student = 'Student';
    
    //
    public static function check($type)
    {
        switch ($type) {
            case RoleType::Root:
            case RoleType::Admin:
            case RoleType::Manager:
            case RoleType::Test:
            case RoleType::Expert:
            case RoleType::Teacher:
            case RoleType::Parent:
            case RoleType::Student:
                return true;
            default:
                return false;
        }
    }
}
