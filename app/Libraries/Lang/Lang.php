<?php
/**
 * Created by PhpStorm.
 * User: ares
 * Date: 2019-07-23
 * Time: 11:32
 */

namespace App\Libraries\Lang;


use App\Models\Locale;
use Illuminate\Support\Facades\Redis;

trait Lang
{
    protected $browserLang;

    /**
     * 讀取瀏覽億語系
     * Lang constructor.
     */
    public function __construct()
    {
        $browser_lang      = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'zh-TW';
        $this->browserLang = strtolower(strtok(strip_tags($browser_lang), ','));
    }

    public function getBrowserLang()
    {
//        return Cache::get('local');
//            switch ($this->browserLang) {
//                case 'en-us':
//                    $this->browserLang = 'en';
//                    break;
//                case 'zh-tw':
//                    $this->browserLang = 'tw';
//                    break;
//                default:
//                    $this->browserLang = 'cn';
//                    break;
//            };
//            app()->setLocale($this->browserLang);
//            session()->put('locale', $this->browserLang);

//            return $this->browserLang;
//
//        } else {
//            return app()->getLocale();
//        }
    }

    /**
     * 將語系 字串轉換與DB相同
     *
     * @param $lang
     * @return integer
     */
    public function getConvertByLangString($lang)
    {
        switch ($lang) {
            case 'en':
                return Locale::query()->where('type', 'en-US')->pluck('id')->first();
            case 'tw':
                return Locale::query()->where('type', 'zh-TW')->pluck('id')->first();
            case 'cn':
                return Locale::query()->where('type', 'zh-CN')->pluck('id')->first();
        }
    }

    /**
     * 將語系 字串轉換與大寫
     * @param $lang
     * @return string
     */
    public static function getConvertByLang($lang)
    {
        switch ($lang) {
            case 'en':
                return 'en-US';
            case 'tw':
                return 'zh-TW';
            case 'cn':
                return 'zh-CN';
        }
    }

    /**
     * 將語系 字串轉換與DB相同
     *
     * @return integer
     */
    public static function getConvertByLangStringForId()
    {
        switch (Redis::get('local')) {
            case 'en-US':
                return Locale::query()->where('type', 'en-US')->pluck('id')->first();
            case 'zh-TW':
                return Locale::query()->where('type', 'zh-TW')->pluck('id')->first();
            case 'zh-CN':
                return Locale::query()->where('type', 'zh-CN')->pluck('id')->first();
        }
    }


}
