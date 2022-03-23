<?php

namespace App\Types\Video;

abstract class MarkType
{
    /*
     * 0:hard(難點);1:star(重點)
     * PS:hard/star定義是由HiTeach決定
     * */
    //
    const Hard = 0;
    const Star = 1;
    
    //
    public static function check($type)
    {
        switch ($type) {
            case MarkType::Hard:
            case MarkType::Star:
                return true;
            default:
                return false;
        }
    }
    
    //
    public static function list()
    {
        return [
            ['type' => 'Hard', 'value' => MarkType::Hard, 'text' => '難點'],
            ['type' => 'Star', 'value' => MarkType::Star, 'text' => '重點'],
        ];
    }
    
}
