<?php

namespace Tests\Feature;

use App\Helpers\Custom\GlobalPlatform;
use App\Libraries\Azure\Blob;
use App\Models\ObservationClass;
use App\Services\TagTypeService;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;
use Tests\TestCase;

class BlobTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_app()
    {
        // 0 國際戰
        // 9 大陸站
        $classroom_code = 0 . mt_rand(1, 99999);
//        dd($classroom_code);
//        dd($classroom_code);
//        dd($model);
    }

    public function test_get_sas()
    {
        $account           = getenv('BLOB_ACCOUNT');
        $key               = getenv('BLOB_KEY');
        $endpoint          = getenv('ENDPOINT');
        $connectionsString = "DefaultEndpointsProtocol=https;AccountName=$account;AccountKey=$key;EndpointSuffix=$endpoint";

        $blobClient                       = BlobRestProxy::createBlobService($connectionsString);
        $container                        = getenv('BLOB_VIDEO_CONTAINER');
        $adapter                          = new AzureBlobStorageAdapter($blobClient, $container);
        $filesystem                       = new Filesystem($adapter);
        $tableSharedAccessSignatureHelper = new BlobSharedAccessSignatureHelper($account, $key);

        $expiryHour     = 24;
        $expiryDateTime = Carbon::now()->addHour($expiryHour)->toDate();
        $startDateTime  = Carbon::now()->subHour(1)->toDate();

        $blob        = "temp/";
        $generaToken = $tableSharedAccessSignatureHelper->generateBlobServiceSharedAccessSignatureToken(
            'b',
            "$container/temp",
            'r',
            $expiryDateTime,
            $startDateTime
        );


        dd($generaToken);
    }

    public function test_update()
    {
        $blob = 'temp/observation.json';
//        $blob      = 'temp / comment . json';
        $container = getenv('BLOB_VIDEO_CONTAINER');


        $observation = ObservationClass::query()->first();

        $jsonData = json_encode([
            'className'       => $observation->name,
            'classTeacher'    => $observation->teacher,
            'classTeacher_id' => $observation->habook_id,
            'duration'        => $observation->duration,
            'status'          => $observation->status,
            'timestamp'       => Carbon::parse($observation->timestamp)->timestamp,
        ]);
//        $jsonData = json_encode(['test' => 1]);
        $tempArray   = json_decode($jsonData, true);
        $array_merge = array_merge($tempArray, ['duration' => 48000]);
        $jsonData    = json_encode($array_merge);
//        file_put_contents('observation . json', $jsonData);
        $blobServer = new Blob(getenv('BLOB_ACCOUNT'), getenv('BLOB_KEY'), getenv('ENDPOINT'));

        $blobServer->update($blob, $container, $jsonData, false);


        dd(1);

    }

    public function test_urlAndSas()
    {
        $blobServer = new Blob(getenv('BLOB_ACCOUNT'), getenv('BLOB_KEY'), getenv('ENDPOINT'));

//        $test = 'TSDFDSFdsfsds';
//        dd(\Str::lower($test));
//        dd($blobServer->getContainerUrl(getenv('BLOB_VIDEO_CONTAINER')) . '/observation');
        $result = $blobServer->getSas('', getenv('BLOB_VIDEO_CONTAINER'), 36500, 'lcr', 'c');
        dd($result);

    }

}
