<?php

namespace App\Types\Tba;

use Illuminate\Support\Facades\Lang;
use App\Helpers\Enum\Item;

abstract class IdentityType
{
    use Item;

    //
    const Expert = 'E';
    const Visitor = 'V';
    const Teacher = 'T';
    const User = 'U';
    const Guest = 'G';

    //
    public static function check($type)
    {
        switch ($type) {
            case IdentityType::Expert:
            case IdentityType::Visitor:
            case IdentityType::Teacher:
            case IdentityType::User:
            case IdentityType::Guest:
                return true;
            default:
                return false;
        }
    }

    //
    public static function list()
    {
        $mapTbaIdentity = Lang::get('app/tba/identity');
        return [
            ['type' => 'Expert', 'value' => IdentityType::Expert, 'text' => $mapTbaIdentity['Expert']],
            ['type' => 'Visitor', 'value' => IdentityType::Visitor, 'text' => $mapTbaIdentity['Visitor']],
            ['type' => 'Teacher', 'value' => IdentityType::Teacher, 'text' => $mapTbaIdentity['Teacher']],
            ['type' => 'User', 'value' => IdentityType::User, 'text' => $mapTbaIdentity['User']],
            ['type' => 'Guest', 'value' => IdentityType::Guest, 'text' => $mapTbaIdentity['Guest']],
        ];
    }

}
