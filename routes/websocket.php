<?php


use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Facades\Websocket;

/*
|--------------------------------------------------------------------------
| Websocket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register websocket events for your application.
|
*/


Websocket::on('connect', function ($websocket, Request $request) {
//     called while socket on connect
    $websocket->emit('message', 'success');
    echo 'connect' . PHP_EOL;
//    $websocket->emit('我是伺服器這邊連結成功的訊息.', 123);
});

//Websocket::on('disconnect', function ($websocket) {
////     called while socket on disconnect
//    echo 'disconnect' . PHP_EOL;
//    $websocket->emit('close.', 123);
//});

//Websocket::on('test', function ($websocket, $data) {
//    echo 'message';
//    $websocket->emit('message', '斷開連線');
//    $websocket->broadcast()->emit('example', 'this is a test');
//});
/**
 * 定义一个login路由，指向控制器方法，和http类似
 */
//Websocket::on('login', 'App\Http\Controllers\Index\LoginController@index');
//Websocket::on('observation', 'App\Http\Controllers\Api\V1\Groups\ObservationController@index');
