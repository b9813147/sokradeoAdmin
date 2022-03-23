<?php

namespace Tests\Feature;

use Tests\TestCase;

class LessonTest extends TestCase
{
    public function testBasic()
    {
        $this->withHeaders([
            'Accept'        => 'application/vnd.sokradeo.v1+json',
            'application'   => 'c1e3deb8-9c20-49b3-9508-109911e5b984',
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhY2NvdW50LnRlYW1tb2RlbCIsInN1YiI6IjE1MjU2NTgzNzciLCJhdWQiOiJkNzE5Mzg5Ni05YjEyLTQwNDYtYWQ0NC1jOTkxZGQ0OGNjMzkiLCJleHAiOjE2MzczMDY3NjUsImlhdCI6MTYzNzIyMDM2NSwibm9uY2UiOiIwIiwibmFtZSI6IkFyZXMiLCJwaWN0dXJlIjoiaHR0cHM6Ly9jb3Jlc3RvcmFnZXNlcnZpY2UuYmxvYi5jb3JlLmNoaW5hY2xvdWRhcGkuY24vYWNjb3VudC9hdmF0YXIvMTUyNTY1ODM3NyIsImlzQWN0aXZhdGUiOiJ0cnVlIiwiYXJlYSI6Imdsb2JhbCIsIm5iZiI6MTYzNzIyMDM2NX0.65teilQTtNDrAuPTk6IsfSVioSPlkPBUJG9Dsb1_XC8'
        ]);

        $testResponse = $this->post('api/school/video',
            [
                'blobUri' => 'https://teammodel.blob.core.windows.net/1525658377/records/256608031477993472',
                'zipFile' => new \Illuminate\Http\UploadedFile(public_path('256608031477993472.zip'), '256608031477993472.zip', null, null, true),
            ]
        );

        $testResponse->dump();
        $testResponse->assertStatus(200);
    }
}
