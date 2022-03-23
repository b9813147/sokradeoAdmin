<?php

namespace App\Helpers\Enum;

trait Constant
{
    /**
     * @return mixed
     */
    public static function getConstant($v)
    {
        try {
            return constant('self::' . $v);
        } catch (\Exception $exception) {
            return false;
        }

    }

}
