<?php

namespace App\Helpers\Path;

trait File
{
    /**
     * @return string
     */
    public static function pathFile($fileId = null, $absolute = false)
    {
        $path = 'file/' . (is_null($fileId) ? '' : $fileId);
        return $absolute ? storage_path('app/'.$path) : $path;
    }
    
}