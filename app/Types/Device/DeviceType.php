<?php

namespace App\Types\Device;

abstract class DeviceType
{
    const App = 'App';
    const HiTeach = 'HiTeach';
    const Web = 'Web';

    /**
     * @param $type
     * @return bool
     */
    public static function check($type): bool
    {
        switch ($type) {
            case DeviceType::App:
            case DeviceType::HiTeach:
            case DeviceType::Web:
                return true;
            default:
                return false;
        }
    }
}
