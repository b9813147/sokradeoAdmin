<?php

namespace App\Helpers\Enum;

trait Item
{
    /**
     * @return mixed
     */
    public static function getItem($val)
    {
        $list = collect(self::list());
        $idx  = $list->search(function ($v) use ($val) {
            return $v['value'] === $val;
        });
        return $list[$idx];
    }
    
}