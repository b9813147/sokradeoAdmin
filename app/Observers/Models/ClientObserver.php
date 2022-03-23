<?php

namespace App\Observers\Models;

use App\Models\Client;

class ClientObserver
{

    public function creating(Client $client) // 待防呆 若重複須重新產生
    {
        $client->id     = uniqid();
        $client->secret = $this->generateSecret();
    }

    private function generateSecret($length = 16) // 待懮化 提取成為Helper
    {
        $secret      = '';
        $chars       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxCharsIdx = strlen($chars) - 1;
        for ($i = 0 ; $i < $length ; $i++) {
            $secret .= $chars[rand(0, $maxCharsIdx)];
        }
        return $secret;
    }

}
