<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->put('/api/group/604', [
            'school_code' => 'HBTMTEST1111',
            'name'        => '網奕資訊研發部',
            'description' => 'test',
//            'abbr'        => HBTMTESTEN % 5C % 5C, //'review_status'=> 0,
//'public_note_status'=> 1,
//'school_upload_status'=> 1,
//'public'=> 0,
        ]);

        $response->assertStatus(200);
    }

    public function test_join()
    {
        $this->withHeaders([
            'Accept'        => 'application/vnd.sokradeo.v1+json',
            'application'   => 'c1e3deb8-9c20-49b3-9508-109911e5b984',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiYTIyYTgyNGMxZDY1NTIwYzFjNjkwOTI5YzgzZjY0MGViZWEwZWQzZjM2OTZmOTU2MDY1MzRlMGUzNzg4YTE2Nzk2MDZlYjU3MzBiMDhhODUiLCJpYXQiOjE2NDQ4MjYxNzgsIm5iZiI6MTY0NDgyNjE3OCwiZXhwIjoxNjQ2MTIyMTc4LCJzdWIiOiIiLCJzY29wZXMiOlsiKiJdfQ.avz15uX_nJUb5RsTlayZ64b5Nd1JAx0ZZwsxgLA8jKkhRFefdm3pZdRmUDiHN1Z_DFiYeC1oXD1go3XoByLZSl_lJLXwqOECffwu-DWskhty3_z0qSt7TC6rXpbIZ2CPw5GslclegfupF_k-Cx-35N7ATYWODC75dGV5pJLHkY3M-teDXU0HmfayLLKaxtfQpfZYnde9tLYS2VmtSn2V0BM8110f3JAiuHmi9zdi_Te5RvjzbMio8bFev591j73BfAeaV-fSXmxD2xOPNpLWqKE-u2hBKomywXZdPMuPI-nwj_83Ft61iMc4VO-ylfgnDfzAlv15PXhUHKbVVpZxEqDMluXLM7V6Y7Wf4vkrBsapBtGOKbxd7UeR21niN4hBASG416hGJlFuBZ8mQQmQWCyhwd2MoTgIYf6OcwQTjEqUX7uLjA2xobYzchQXr4NgrW75wrsQP4Kj1weFlZvDak4J-NPMMZhFrrkmgwHoIXaURmlG0eI8RKZZhCMw6_YEPLvrfznhVyAErbMocufdj1e72xsjLPY_TFwaPEVzj1dDB4nHcZ7UP9eDCb1qKFxVNCMowmnuzn8TDrcSl6x4rf3G0Qk2pOm-KJS1paC5UDa9Wa1nBQBitxHwlNTBGfo4Lp44iB2lgVtq9K8uCpzDHHTiZakPV17xLoGuyCmzFNk'
        ]);
        $data = [
            [
                'abbr'         => 'WHXFXX',
                'duty'         => 'admin',
                'teammodel_id' => 1638343573,
                'name'         => 'Terry'
            ],
            [
                'abbr'         => 'WHXFXX',
                'duty'         => 'admin',
                'teammodel_id' => 1629875867,
                'name'         => '趙詠真'
            ]
        ];
        $response = $this->post('/api/school/group', $data);
        $response->dump();
        $response->assertStatus(204);
    }

    public function test_remove_join()
    {
        $this->withHeaders([
            'Accept'        => 'application/vnd.sokradeo.v1+json',
            'application'   => 'c1e3deb8-9c20-49b3-9508-109911e5b984',
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhY2NvdW50LnRlYW1tb2RlbCIsInN1YiI6IjE1MjU2NTgzNzciLCJhdWQiOiJkNzE5Mzg5Ni05YjEyLTQwNDYtYWQ0NC1jOTkxZGQ0OGNjMzkiLCJleHAiOjE2MzczMDY3NjUsImlhdCI6MTYzNzIyMDM2NSwibm9uY2UiOiIwIiwibmFtZSI6IkFyZXMiLCJwaWN0dXJlIjoiaHR0cHM6Ly9jb3Jlc3RvcmFnZXNlcnZpY2UuYmxvYi5jb3JlLmNoaW5hY2xvdWRhcGkuY24vYWNjb3VudC9hdmF0YXIvMTUyNTY1ODM3NyIsImlzQWN0aXZhdGUiOiJ0cnVlIiwiYXJlYSI6Imdsb2JhbCIsIm5iZiI6MTYzNzIyMDM2NX0.65teilQTtNDrAuPTk6IsfSVioSPlkPBUJG9Dsb1_XC8'
        ]);
        $teammodel_ids = [1629875867, 1638343573];
        $abbr          = 'WHXFXX';
        $response      = $this->delete('/api/school/group/' . $abbr, $teammodel_ids);
        $response->dump();
        $response->assertStatus(204);
    }
}
