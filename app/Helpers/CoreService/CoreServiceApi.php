<?php

namespace App\Helpers\CoreService;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait CoreServiceApi
{
    /**
     * 確認用戶是否存在
     * @param string $haBook
     * @return bool
     */
    public function isExist(string $haBook): bool
    {
        try {
            $response = collect($this->client()->post(getenv('HABOOK_2_API_URL') . 'oauth2/GetUserInfos', [
                $haBook
            ])->json())->isNotEmpty();
        } catch (\Exception $exception) {
            Log::error('GetUserInfos', [$exception->getMessage()]);
            $response = false;
        }
        return $response;
    }

    /**
     * @param string $id_token
     * @return array|mixed
     */
    public function getSso(string $id_token)
    {
        $url        = getenv('HABOOK_2_API_URL') . 'oauth2/login';
        $grant_type = 'code';
        $nonce      = '0';
        try {
            return Http::post($url, [
                'grant_type' => $grant_type,
                'client_id'  => config(sprintf('habook.core2.%s.client_id', getenv('APP_STATION'))),
                'nonce'      => $nonce,
                'id_token'   => $id_token,
            ])->json()['code'];
        } catch (\Exception $exception) {
            return $exception->getMessage();

        }
    }

    /**
     * 下載縮圖
     * @param string $url
     * @param string $name
     * @param string $sas
     * @param string $record_id
     * @return bool
     */
    public function downloadThumbnail(string $url, string $name, string $sas, string $record_id): bool
    {
        $isSuccessful = true;
        try {
            $url_image  = "'$url/Record/$name?$sas'";
            $url_target = storage_path('temp/lesson/' . $record_id . '/Sokrates');
            exec('/var/www/azcopy copy ' . $url_image . ' ' . $url_target . ' --recursive   --output-type text');
        } catch (\Exception $exception) {
            $isSuccessful = false;
        }

        return $isSuccessful;
    }

    /**
     * 下載蘇格拉底
     * @param string $url
     * @param string $sas
     * @param string $record_id
     * @return bool
     */
    public function downloadSokradeo(string $url, string $sas, string $record_id): bool
    {
        $isSuccessful = true;

        try {
            $url_resource_sokrodeo = "'$url/Sokrates?$sas'";
            $url_target            = storage_path('temp/lesson/' . $record_id);
            exec('/var/www/azcopy copy ' . $url_resource_sokrodeo . ' ' . $url_target . ' --recursive   --output-type text');
        } catch (\Exception $exception) {
            $isSuccessful = false;
        }

        return $isSuccessful;
    }

    /**
     * 取得 Blob Sas
     * @param string $idToken
     * @param string $abbr
     * @return mixed|string
     */
    public function getBlobSas(string $idToken, string $abbr)
    {
        $url = getenv('HABOOK_2_BLOB_URL') . 'sokrate/get-blob-read';

        try {
            $response = Http::withHeaders([
                'X-Auth-IdToken' => $idToken,
            ])
                ->withOptions(['verify' => false])
                ->post($url, ['school_code' => $abbr])->json();
            $response = empty($abbr) ? $response['teacher_blob_sas_read'] : $response['school_blob_sas_read'];
        } catch (\Exception $exception) {
            $response = null;
        }

        return $response;
    }

    /**
     * get user information
     * @param string $id_token
     * @return array|mixed|null
     */
    public function getUserInformation(string $id_token)
    {
        $url  = getenv('HABOOK_2_API_URL') . 'oauth2/usersetting';
        $body = json_encode([
            'grant_type' => 'get',
            'id_token'   => $id_token,
            'product'    => 'sokapp'
        ]);

        try {
            $response = $this->client()->post($url, [
                'body' => $body,
            ])->json();
        } catch (\Exception $exception) {
            $response = null;
        }
        return $response;
    }

    /**
     * type  notice | msg | info
     * body
     * json_encode([
     *     "content" => "2021創新獎開始收件,歡迎智慧教師報名參加\n請於110年7月31日前將課例作品提交, 敬請請報名",
     *     "action"  => [
     *         [
     *             "type"          => "click",
     *             "label"         => "我要報名",
     *             "url"           => "https://sokrates.teammodel.org/exhibition/tbavideo#/activity-channel/534",
     *             "tokenbindtype" => 1,
     *         ],
     *     ],
     * ])
     * @param array $teamMode_ids
     * @param string $body
     * @param string $label
     * @param bool $status
     * @param string $type
     * @param string $form
     * @return bool
     */
    public function sendNotify(array $teamMode_ids, string $body, string $label, bool $status = true, string $type = 'msg', string $form = 'Sokrates'): bool
    {
        // 決定是否發通知
        if (!$status) {
            return false;
        }
        $isSuccessFul = true;
        $url          = getenv('HABOOK_2_API_URL') . '/service/sendnotification';
        $date         = Carbon::now()->utc()->addDay(100)->timestamp;
        $parameter    = [
            'hubName' => 'hita',
            'type'    => $type,
            'from'    => $form,
            'to'      => $teamMode_ids,
            'label'   => $label,
            'body'    => $body,
            'expires' => $date,
        ];

        try {
            Log::info('App Notify Message', [
                $this->client()->post($url, $parameter)->json(),
                $teamMode_ids,
            ]);
        } catch (\Exception $exception) {
            Log::info('notify', ['status' => 0, 'message' => $exception->getMessage()]);
            $isSuccessFul = false;
        }

        return $isSuccessFul;
    }

    /**
     * 增加 access_token
     * @return \Illuminate\Http\Client\PendingRequest
     */
    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            'content-type'  => 'application/json',
            'authorization' => sprintf('Bearer %s', $this->accessToken()),
        ]);
    }


    /**
     * 取assess_token
     * @return mixed|null
     */
    protected function accessToken()
    {
        $isSuccessFul = null;
        try {
            $url          = getenv('HABOOK_2_API_URL') . 'oauth2/token';
            $json         = Http::post($url, [
                'grant_type'    => 'device',
                'client_id'     => config(sprintf('habook.core2.%s.client_id', getenv('APP_STATION'))),
                'client_secret' => config(sprintf('habook.core2.%s.client_secret', getenv('APP_STATION'))),
            ])->json();
            $collect      = collect($json)->get('access_token');
            $isSuccessFul = $collect;
        } catch (\Exception $exception) {
            Log::info('access_token', ['status' => 0, 'message' => $exception->getMessage()]);
        }
        return $isSuccessFul;
    }
}
