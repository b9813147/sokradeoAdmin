<?php
/**
 * Created by PhpStorm.
 * User: steven
 * Date: 2019-03-25
 * Time: 13:21
 */

namespace App\Helpers\Code;


trait AlphaNumeric
{
    /**
     * @param int $length
     * @return string
     */
    public static function generateNum($length = 8)
    {
        $code        = '';
        $chars       = '0123456789';
        $maxCharsIdx = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[rand(0, $maxCharsIdx)];
        }
        return $code;
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generateAlpha($length = 8)
    {
        $code        = '';
        $chars       = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxCharsIdx = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[rand(0, $maxCharsIdx)];
        }
        return $code;
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generateAlphaNum($length = 8)
    {
        $code        = '';
        $chars       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxCharsIdx = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[rand(0, $maxCharsIdx)];
        }
        return $code;
    }
}
