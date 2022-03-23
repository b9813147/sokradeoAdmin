<?php

namespace App\Helpers\Path;

trait Tba
{
    /**
     * @return string
     */
    public static function pathPublicTba($tbaId, $subPath = null, $absolute = false)
    {
        $path = str_replace('{{tbaId}}', $tbaId, 'public/tba/{{tbaId}}/');
        if (! is_null($subPath)) {
            $path .= $subPath . '/';
        }
        return $absolute ? storage_path('app/'.$path) : $path;
    }
    
    /**
     * @return string
     */
    public static function pathTba($tbaId, $subPath = null, $absolute = false)
    {
        $path = str_replace('{{tbaId}}', $tbaId, 'tba/{{tbaId}}/');
        if (! is_null($subPath)) {
            $path .= $subPath . '/';
        }
        return $absolute ? storage_path('app/'.$path) : $path;
    }
    
    /**
     * @return string
     */
    public static function pathTbaEvalEventFile($tbaId, $subPath = null, $absolute = false)
    {
        $path = self::pathTba($tbaId).'evaluate_event_file/';
        if (! is_null($subPath)) {
            $path .= $subPath . '/';
        }
        return $absolute ? storage_path('app/'.$path) : $path;
    }
    
    /**
     * @return string
     */
    public static function pathTbaStatistic($tbaId, $subPath = null, $absolute = false)
    {
        $path = self::pathTba($tbaId).'statistic/';
        if (! is_null($subPath)) {
            $path .= $subPath . '/';
        }
        return $absolute ? storage_path('app/'.$path) : $path;
    }
    
}