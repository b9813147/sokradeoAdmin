<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace'  => 'App\Http\Controllers\Api\V1',
    'middleware' => ['api.throttle'],
    'limit'      => 5000,
    'expires'    => 0.5,
], function ($api) {
    // 頻道管理
    $api->group(['prefix' => 'group', 'middleware' => 'auth:api'], function ($api) {
        $api->get('{group_id}', 'Groups\GroupController@show');
        //  更新 頻道資訊
        $api->post('update/{group_id}', 'Groups\GroupController@update');
        $api->put('{group_id}', 'Groups\GroupController@update');
        $api->patch('{group_id}', 'Groups\GroupController@update');
        //  新增群組
        $api->post('', 'Groups\GroupController@store');
        $api->get('', 'Groups\GroupController@index');
        // 檢查頻道使用者群不存在
        $api->get('user/exist', 'Groups\GroupController@getMemberGroupExist');
        // 取得使用者資訊
        $api->get('member/{userId}', 'Groups\MemberController@show');
        // 頻道使新增用者
        $api->post('member', 'Groups\MemberController@store');
        // 更新頻道使用者
        $api->put('member/{userId}', 'Groups\MemberController@update');
        // 刪除頻道使用者
        $api->delete('member/{userId}', 'Groups\MemberController@destroy');
        // 學科資訊
        $api->get('subjects/{groupId}', 'Groups\SubjectController@show');
        // 更新學科
        $api->put('subjects/{id}', 'Groups\SubjectController@update');
        $api->put('subjects/sort/{id}', 'Groups\SubjectController@updateSort');
        // 刪除學科
        $api->delete('subjects/{id}', 'Groups\SubjectController@destroy');
        // 新增學科
        $api->post('subjects', 'Groups\SubjectController@store');
        // 取得影片資訊
        $api->get('channel/content/{groupId}', 'Groups\ChannelContentController@show');
        // 刪除影片
        $api->delete('channel/content/{groupId}', 'Groups\ChannelContentController@destroy');
        // 更新影片資訊
        $api->post('channel/content/{groupId}', 'Groups\ChannelContentController@update');
        // 分享課例(新增課例)
        $api->post('share/channel/content/{groupId}', 'Groups\ChannelContentController@store');
        // 課例器連結
        $api->post('lesson/example/{groupId}', 'Groups\ChannelContentController@lessonPlayer');
        // C分數
        $api->put('channel/content', 'Groups\ChannelContentController@editScore');
        // 年級資訊
        $api->get('grade/lang', 'Groups\GradeController@index');
        // 教研評比
        $api->get('rating/{groupId}', 'Groups\RatingController@show');
        // 新增教研
        $api->post('rating', 'Groups\RatingController@store');
        // 更新教研
        $api->put('rating/{ratingId}', 'Groups\RatingController@update');
        $api->put('rating/sort/{ratingId}', 'Groups\RatingController@updateSort');
        // 刪除教研
        $api->delete('rating/{ratingId}', 'Groups\RatingController@destroy');
        // 頻道使用者數據分析
        $api->get('channel/user/stats/{groupId}', 'Groups\GroupChannelController@filter');
        //取得頻道資訊
        $api->get('channel/{id}', 'Groups\GroupChannelController@show');

        //發送活動通知
        $api->post('notification', 'Notification\EventNotificationController@store');
        $api->put('notification/{id}', 'Notification\EventNotificationController@update');
        $api->get('notification/{id}', 'Notification\EventNotificationController@show');
        $api->delete('notification/{id}', 'Notification\EventNotificationController@destroy');

        // 分組管理
        $api->get('division/{channel_id}', 'Division\DivisionController@show');
        $api->post('division', 'Division\DivisionController@store');
        $api->put('division/{id}', 'Division\DivisionController@update');
        $api->delete('division/{id}', 'Division\DivisionController@destroy');

        // 標籤管理
        $api->post('tag', 'Tags\TagController@store');
        $api->put('tag/{id}', 'Tags\TagController@update');
        $api->delete('tag/{id}', 'Tags\TagController@destroy');
        // 焦點類型
        $api->post('tag/type', 'Tags\TagTypeController@store');
        $api->put('tag/type/{id}', 'Tags\TagTypeController@update');
        $api->delete('tag/type/{id}', 'Tags\TagTypeController@destroy');

        $api->resource('qrcode', 'Groups\QrCodeController');
    });
    // 學區
    $api->group(['prefix' => 'district', 'middleware' => 'auth:api'], function ($api) {
        // 學區管理
        $api->get('{districtId}', 'District\DistrictController@show');
        $api->get('/', 'District\DistrictController@index');
        $api->put('/{districtId}', 'District\DistrictController@update');
        $api->post('/', 'District\DistrictController@store');
        // 成員管理
        $api->get('{districtId}/user', 'District\DistrictUserController@show');
        $api->put('/user/{districtId}', 'District\DistrictUserController@update');
        $api->post('/user', 'District\DistrictUserController@store');
        $api->delete('/user/{districtId}', 'District\DistrictUserController@destroy');
        $api->get('/user/exist', 'District\DistrictUserController@userExist');
        // 課例管理
        $api->get('/channel/content/{districtId}', 'District\DistrictChannelContentController@show');
        $api->put('/channel/content/{id}', 'District\DistrictChannelContentController@update');
        $api->delete('/channel/content/{id}', 'District\DistrictChannelContentController@destroy');

        // 學科管理
        $api->get('/subjects/{districtId}', 'District\DistrictSubjectController@show');
        $api->put('/subjects/{id}', 'District\DistrictSubjectController@update');
        $api->post('/subjects', 'District\DistrictSubjectController@store');
        $api->delete('/subjects/{id}', 'District\DistrictSubjectController@destroy');
        $api->put('subjects/sort/{id}', 'District\DistrictSubjectController@updateSort');

        //歸類管理
        $api->get('/classification/{districtId}', 'District\DistrictClassificationController@show');
        $api->put('/classification/{id}', 'District\DistrictClassificationController@update');
        $api->post('/classification', 'District\DistrictClassificationController@store');
        $api->delete('/classification/{id}', 'District\DistrictClassificationController@destroy');
//        $api->put('classification/sort/{districtId}', 'District\DistrictClassificationController@updateSort');

        //分類管理
        $api->get('/rating/{districtId}', 'District\RatingController@show');
        $api->put('/rating/{id}', 'Groups\RatingController@update');
        $api->post('/rating', 'Groups\RatingController@store');
        $api->delete('/rating/{id}', 'Groups\RatingController@destroy');
        $api->put('rating/sort/{id}', 'Groups\RatingController@updateSort');
    });
    $api->group(['prefix' => 'global', 'middleware' => 'auth:api'], function ($api) {
        // 推薦影片
        $api->get('/recommendedVideo', 'Globals\RecommendedVideoController@index');
        $api->post('/recommendedVideo', 'Globals\RecommendedVideoController@store');
        $api->put('/recommendedVideo/{id}', 'Globals\RecommendedVideoController@update');
        $api->delete('/recommendedVideo/{id}', 'Globals\RecommendedVideoController@destroy');
        $api->put('/recommendedVideo/sort/{id}', 'Globals\RecommendedVideoController@updateSort');
        //所有頻道
        $api->get('/recommendedVideo/channel', 'Globals\RecommendedVideoController@getChannels');
        //頻道內未推薦影片
        $api->get('/recommendedVideo/channelContent/{channelId}', 'Globals\RecommendedVideoController@getChannelContents');

        $api->get('/event/channel', 'Groups\GroupController@getEventChannel');
        //發送通知
        $api->post('/notification', 'Notification\NotificationController@store');
    });


    // 外部使用
    $api->group(['prefix' => 'school', 'middleware' => 'auth:team_model'], function ($api) {
        $api->get('/dashboard/{abbr}', 'App\Dashboard\DashboardController@show');
        $api->post('/video', 'App\Video\LessonController@store');
        $api->get('/me', 'App\Video\MobileController@me');
        $api->get('/observation/{school_shortcode}', 'App\School\ObservationController@show');
        $api->get('/observation/class/{classroom_code}', 'App\School\ObservationClassController@show');
        $api->post('member', ['as' => 'member.store', 'uses' => 'Groups\MemberController@store']);
//        $api->post('/user','')
//        $api->post('/observation/class', 'App\School\ObservationClassController@store');

//        });

    });
    // Server to Server
    $api->group(['middleware' => 'client'], function ($api) {
        $api->group(['prefix' => 'school'], function ($api) {
            $api->get('/observation/class/guest/{classroom_code}', 'App\School\ObservationClassController@show');
            // 加入
            $api->post('/group', ['as' => 'group.join', 'uses' => 'Groups\GroupController@join']);
            $api->delete('/group/{abbr}', 'Groups\GroupController@removeJoin');
            $api->put('/group/{abbr}', 'Groups\GroupController@updateJoin');
        });

        // 序號
        $api->post('bb/license', 'App\BB\LicenseController@store');
        $api->delete('bb/license/{code}', 'App\BB\LicenseController@destroy');
    });

    //dashboard
    $api->get('dashboard/{group_id}', 'Dashboard\DashboardController@show');

    // 加入活動頻道
    $api->post('event/join', 'Events\EventController@JoinEventUserByChannels');
    // 新增活動頻道
    $api->post('event/add', 'Events\EventController@addEventChannel');
    // 新增學校頻道
    $api->post('school/add', 'Events\SchoolChannelController@addSchoolChannel');

    // 統計數量
    $api->get('Statistical', 'Statistical\StatisticalController@index');

    // 取得語系
    $api->get('lang/setLocal', 'Lang\LangController@setLocal');
    $api->get('lang/getLocal', 'Lang\LangController@getLocal');

});
//Route::get('qrcode', 'Api\V1\Groups\QrCodeController@index');
//Route::get('qrcode/show', 'Api\V1\Groups\QrCodeController@show');
//$api->get('qrcode', 'Groups\QrCodeController@index');

// 檢查頻道使用者群不存在
//Route::get('group/user/exist', 'Api\GroupController@getMemberGroupExist');
//Route::get('users', 'Api\UserController@getUserAll');

// 頻道使用者
//Route::post('group/member', 'Group\GroupMemberController@store');
//Route::put('group/member/{userId}', 'Group\GroupMemberController@update');
//Route::delete('group/member/{userId}', 'Group\GroupMemberController@destroy');



