<?php

namespace App\Http\Controllers\Api\V1\Lang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class LangController extends Controller
{
    public function setLocal(Request $request)
    {
        $set = Redis::set('local', $request->input('lang'));
        $message = [
            'message' => $set
        ];

        return response()->json($message, 200);
    }

    public function getLocal()
    {
        $message = [
            'lang' => Redis::get('local')
        ];

        return response()->json($message, 200);
    }
}
