<?php

namespace App\Types\Auth;

abstract class AccountType
{
    //
    const Habook = 'Habook';
    const Client = 'Client';

    //
    public static function check($type)
    {
        switch ($type) {
            case AccountType::Habook:
            case AccountType::Client:
                return true;
            default:
                return false;
        }
    }

    //
    public static function getDbColNameByAccType($type = null)
    {
        if (is_null($type)) {
            return ['habook', 'client_id', 'client_user'];
        }

        switch ($type) {
            case AccountType::Habook:
                return 'habook';
            case AccountType::Client:
                return ['client_id', 'client_user'];
            default:
                assert(false);
        }
    }

    //
    public static function list()
    {
        return [
            ['type' => 'Habook', 'value' => AccountType::Habook, 'text' => 'Team Model ID'],
            ['type' => 'Client', 'value' => AccountType::Client, 'text' => 'Client'],
        ];
    }

}
