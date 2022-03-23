<?php

namespace App\Helpers\File;

trait Ext
{
    /**
     * @return boolean
     */
    public static function checkImgExt($ext)
    {
        $ext = strtolower($ext);
        return in_array($ext, ['bmp', 'jpeg', 'jpg', 'png']);
    }
    
}