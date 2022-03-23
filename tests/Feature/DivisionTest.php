<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\GroupChannel;
use App\Models\GroupChannelContent;
use PhpParser\Node\Expr\AssignOp\Div;
use Tests\TestCase;

class DivisionTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testCreate()
    {
        $var  = Division::find(15);
//        $var1 = GroupChannel::find(7);
//        dd($var->users->pluck('id'));
        $tbas = [55, 56];
//        $users = [948,7];

//        $var->tbas()->updateExistingPivot($tbas, ['division_id' => $id]);
//        $var->tbas;
        GroupChannelContent::query()->where(['division_id' => 15])->update(['division_id' => null]);
        dd($var->tbas);



//        dd($var1->tbas()->wherePivot('division_id', 16)->division_id('div'));
//        dd($var->users()->detach($var->users->pluck('id')));
    }
}
