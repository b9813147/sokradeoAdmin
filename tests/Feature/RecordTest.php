<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repositories\RecordRepository;
use App\Types\Record\RecordType;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RecordTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testRecord($type = null, $user_id = null, $tba_id = null, $district_user_id = null, $group_subject_field_id = null, $district_group_subject_id = null, $district_subject_id = null, $district_channel_content_id = null, $rating_id = null, $group_id = null, $user = null)
    {
        $CREATE      = RecordType::CREATE;
        $application = app(RecordRepository::class);
        $application->create([
            'type'    => $CREATE,
            'user_id' => 948,
            'tba_id'  => 3,

        ]);

        dd($application->first()->type);
    }

    public function testAuto()
    {
        $client = new Client();
        $token = User::query()->find(948)->createToken('test')->accessToken;

        dd($token);

        $response = $client->post('http://adminvuetify.com/oauth/authorize', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/vnd.sokradeo.v1+json',
            ],
        ]);
        dd($response->getBody()->getContents());
    }

}
