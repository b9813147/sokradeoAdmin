<?php

namespace Tests\Feature;

use App\Models\TbaAnnex;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TbaAnnexesTest extends TestCase
{
    public function testBasic()
    {
        TbaAnnex::query()->get()->each(function ($q) {
            if (Storage::exists("file/$q->id")) {
                $q->size = Storage::size("file/$q->id");
                $q->save();
            }
        });
    }
}
