<?php

namespace App\Helpers\Path;

trait Video
{
    /**
     * @return string
     */
    public static function pathPublicVideo($videoId, $subPath = null, $absolute = false)
    {
        $path = str_replace('{{videoId}}', $videoId, 'public/video/{{videoId}}/');
        if (! is_null($subPath)) {
            $path .= $subPath . '/';
        }
        return $absolute ? storage_path('app/'.$path) : $path;
    }
    
    /**
     * @return string
     */
    public static function pathPublicVideoIdx($videoId, $subPath = null, $absolute = false)
    {
        $path = str_replace('{{videoId}}', $videoId, 'public/video/{{videoId}}/index/');
        if (! is_null($subPath)) {
            $path .= $subPath . '/';
        }
        return $absolute ? storage_path('app/'.$path) : $path;
    }
    
    /**
     * @return string
     */
    public static function pathVideo($videoId, $subPath = null, $absolute = false)
    {
        $path = str_replace('{{videoId}}', $videoId, 'video/{{videoId}}/');
        if (! is_null($subPath)) {
            $path .= $subPath . '/';
        }
        return $absolute ? storage_path('app/'.$path) : $path;
    }
    
}