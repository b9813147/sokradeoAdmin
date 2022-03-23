<?php
return [
    'url' => env('HABOOK_API_URL','https://api.habookaclass.biz'),
    // 以今日的日期為密鑰對API key字串做sha256的加密運算，生成Hash字串
    // Ex hash_hmac('sha256', 'THISISATESTFORBBAPIKEY', '2019-07-18')
    'key' => '8e66ffa39fcf88c3ba64d18ac8978a6e8fa27a742787594347a98adf49f54d59',
];
