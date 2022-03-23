<?php

namespace Tests\Feature;

use App\Services\GroupService;
use App\Types\Group\DutyType;
use Tests\TestCase;

class GroupServiceTest extends TestCase
{
    public function testBasic()
    {
        $response = app(GroupService::class);
        $data     = collect([
            [
                'duty'         => DutyType::Expert,
                'teammodel_id' => '1525658377',
                'name'         => 'Legend'
            ]
        ]);
        $abbr     = 'hben';

        $response->updateJoin($abbr, $data);

        $response->assertStatus(204);
    }

    public function testPut()
    {
        $access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNGQ5ODVkODVlZmRjMmNhZjgxNmM2OWFkNWJhZDZlY2U5ZjNhMjU1MWRmZGFhZDRmM2NhMTY2ODM3NzUxMTlhOTM3ODE5ZjY4ZjNkNDdjN2MiLCJpYXQiOjE2NDMwODAwNTUsIm5iZiI6MTY0MzA4MDA1NSwiZXhwIjoxNjQ0Mzc2MDU1LCJzdWIiOiIiLCJzY29wZXMiOlsiKiJdfQ.NQg8KRexp8FwO8F-E8Xdty65NApta4UGlaGHINLWKaTZPGuIrv2eC1i4mKT0M30FfR6r6L42nnr09KkCTN8rZqMKIzwi1exjE6t9opkaqAOo640k2COEjbVeto9b--elCyHOBK4-2fhGTPh17dpQtLIqZfGDOyHGsNXg4voih9QFItJP0LFT5FlwfFZWcWpYAWZzIPE3kr8hFv5h8R-Ck3faaTpziXnMYliDOInR-l_dwxf0I7IeDSUv-7mpNQYK3syT5en6TkpoU83lseC1J69NfE1cbzTs3huFwPbgMZGt5KRV9h89bHG0MQVHZCrUyYh1CHd-HBhWjSzC-6Vz2ZdyX2Y8aBk7lN8xhRHKyLtbj_4LQqBOMNu-v_fDKo8Qv8sXCkYh7bWsj9FronlTEExo6dyfDv5J0zsOPJktfNNL8N85TrPyWxeTypQqTIOlftF-JiF5TPjbHdgWBof9SJuFbSGKpNJTuWu4ctrk1aTzcS6_5x8u99ZzP_SqpmKqkbi73O3NTtdhyKTfFXduoV77HNcq2nCOpiyVQy9iv1eIyjOniYcer3IJsRAzM1e-bGA9LPQ9896npjH5hoxfTyjpnkpV2ahgH4PR-0rYPXcEJG3zJQw0ExOz4g1hxeyYuNHQ0J9-HlSjtkSkpe8TUl5oZ1YFU0KPrzQbEr8AGFQ';
        $response     = $this->withHeaders(
            [
                'accept'        => ' application/vnd.sokradeo.v1+json',
                'Authorization' => sprintf('Bearer %s', $access_token),
            ]
        )->put('api/school/group/hben', [
            [
                'duty'         => DutyType::Expert,
                'teammodel_id' => '1525658377',
                'name'         => 'Legend'
            ]
        ]);
        $response->dump();
        $response->assertStatus(204);
    }
}
