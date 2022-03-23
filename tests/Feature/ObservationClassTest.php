<?php

namespace Tests\Feature;

use App\Services\App\ObservationService;
use Tests\TestCase;

class ObservationClassTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testShow()
    {
        $classId = '036799';
        $pinCode = '6124';
        $this->withHeaders([
            'Accept'        => 'application/vnd.sokradeo.v1+json',
            'application'   => 'c1e3deb8-9c20-49b3-9508-109911e5b984',
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhY2NvdW50LnRlYW1tb2RlbCIsInN1YiI6IjE1MjU2NTgzNzciLCJhdWQiOiJkNzE5Mzg5Ni05YjEyLTQwNDYtYWQ0NC1jOTkxZGQ0OGNjMzkiLCJleHAiOjE2MzczMDY3NjUsImlhdCI6MTYzNzIyMDM2NSwibm9uY2UiOiIwIiwibmFtZSI6IkFyZXMiLCJwaWN0dXJlIjoiaHR0cHM6Ly9jb3Jlc3RvcmFnZXNlcnZpY2UuYmxvYi5jb3JlLmNoaW5hY2xvdWRhcGkuY24vYWNjb3VudC9hdmF0YXIvMTUyNTY1ODM3NyIsImlzQWN0aXZhdGUiOiJ0cnVlIiwiYXJlYSI6Imdsb2JhbCIsIm5iZiI6MTYzNzIyMDM2NX0.65teilQTtNDrAuPTk6IsfSVioSPlkPBUJG9Dsb1_XC8'
        ]);
        $testResponse = $this->get('/api/school/observation/class/' . $classId . '?pin_code=' . $pinCode);
        $testResponse->dump();
        $testResponse->assertStatus(200);
    }

    public function test_guest_show()
    {
        $classId = '036799';
        $pinCode = '6124';
        $this->withHeaders([
            'Accept'        => 'application/vnd.sokradeo.v1+json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiODYxNWIxOWEyMDdjYWFlYzM5NDE4ZjkxNzBjNGYzNjE1OTkwNTJjOGE1MDhjYWU5MTZkOWY1ZTExZmE0YTZlNzQ2N2YwZDkyYjlhY2M3MTciLCJpYXQiOjE2NDI1NzgwOTgsIm5iZiI6MTY0MjU3ODA5OCwiZXhwIjoxNjQzODc0MDk4LCJzdWIiOiIiLCJzY29wZXMiOlsiKiJdfQ.F4uFU9Xz9G9kCBPJfEPJFf18rXanShZdB7T5Eh8eYG8MkLKb4mSfBc8YXwZ7gvB5ro4VUrTAfahnPHwOdR_4nJgrAOZgO2qtIPefZOC7PBmYhu-n5JS6q4OuZ57-SPjIZtYtoQWA0q6Ld2tNYxqDaVjq5qO1vyReXb85QHAwylOPHZ4RBVjD56B7iGydX_jqrXxQ7t2_qf1HhRU0ZWcTaeefBqwjyKqqw2hkyqWaUsc2iwTI857pc22vJnMW0oFkD5o9Xb7WZHF9-SxginPdcqeEoC62REAv0-AbcZh_jGmF2HjHx68_Nap9OroRzKSHwjkksXfZ9SQ8yj3FCqnUAYW4hH2ZhnQzV6fodHDod8rta6q_r9LMLUO7Lwec611MG8a6byFkHT8cQ5iAdplgaO5sOsORWR4KJyWWuRSMw-cgVoCOhLZ7RenlwaPTTgqbNw4SCTFvSdeBLunwW9FLcOecD7SaiKKj7ei_gYqiPApbOMynC1R0_8FF3W_fcOjRWjPTNd5oqvUZQAusDSbyOQt-otnnz06LSkoYDea4wAurExUoMUGtdau7TfxtK1p5BwtvcSfhFAzsj9hHXyhmJIoi4QX9mJlD_cFkzfuYofP5e1_eGzPvDdUaXFFrHhpnqUKq7L7t9rV0pqznRkeb9e3v9MWhuj0nYSe9D7wDxe0'
        ]);
        $testResponse = $this->get('/api/school/observation/class/guest/' . $classId . '?pin_code=' . $pinCode . '&guest=12312312');
        $testResponse->dump();
        $testResponse->assertStatus(200);
    }

    /**
     * @test
     */
    public function createLesson()
    {
        $app = app(ObservationService::class);
        dump($app->createLesson(127));
        $this->assertTrue(true);
    }
}
