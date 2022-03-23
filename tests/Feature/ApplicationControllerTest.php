<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;

class ApplicationControllerTest extends TestCase
{
    public function testBasic()
    {
        dd(Str::uuid()->toString());
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
