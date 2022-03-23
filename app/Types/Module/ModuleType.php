<?php


namespace App\Types\Module;


abstract class ModuleType
{
    const Group = 'group';
    const District = 'district';
    const Platform = 'platform';

    public static function check($type)
    {
        switch ($type) {
            case ModuleType::Group:
            case ModuleType::District:
            case ModuleType::Platform:
                return true;
            default:
                return false;
        }
    }
}
