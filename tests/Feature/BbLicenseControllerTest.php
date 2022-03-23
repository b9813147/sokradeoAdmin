<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Tests\TestCase;

class BbLicenseControllerTest extends TestCase
{
    public function test_create()
    {
        $code             = '312323';
        $order_no         = '1312311';
        $school_shortcode = 'HBEN';
        $response         = $this->withAccessToken()->post('/api/bb/license/', [
            'school_shortcode' => $school_shortcode,
            'code'             => $code,
            'order_no'         => $order_no,
            'storage'          => 2048,
            'status'           => 1,
            'start_time'       => Carbon::now()->format('Y-m-d'),
            'deadline'         => Carbon::now()->addYear(1)->format('Y-m-d')
        ]);
        $response->dump();
        $response->assertStatus(204);
    }

    public function test_delete()
    {
        $code             = '312323';
        $order_no         = '1312311';
        $school_shortcode = 'HBEN';
        $response         = $this->withAccessToken()->delete('/api/bb/license/' . $code, [
            'school_shortcode' => $school_shortcode,
            'order_no'         => $order_no,
        ]);
        $response->dump();
        $response->assertStatus(204);
    }

    public function withAccessToken()
    {
        $access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiZGM5ZjU1MjNkY2I0MTRlMjg1YTU0MDMwNTcxMDkxMjAxNzM2N2JhM2I2ZjI3NDgxMTZlZDljNDgzMmFkOTQyOWU1YmY0NzIzNDFiNjEwYzQiLCJpYXQiOjE2NDI0OTE3MjAsIm5iZiI6MTY0MjQ5MTcyMCwiZXhwIjoxNjQzNzg3NzIwLCJzdWIiOiIiLCJzY29wZXMiOlsiKiJdfQ.Z_QrMou3L64oXM5m6lFdIOkEtbxtZf3IIfiXtWJ8THfJN7YUEVJZau6UySmVeQD-4UBU7P4gjrM5YX5wffbTzFVm5slTteM_hpGUQipJNAW0Lj4ggO0szW2IM4bc9S_FrgPaCccOkUYJdEiwTJgoF7-fe9DEvxDHy2qIMT3DGmqnVOdVUYHAR1K3Qix-SKCVGDUKQRCN9mdjX3c9QVm85ncXEo-iLA5KfEjbGDXqpcUMFaNXXCaiSBjK27xPZmDPEi3auYbSJbtk5pWCmHyAW2Bzb--2fFzXxvWzXHKtv942IJSzfLGOyGcq9UyrYsrMmlu2yB2cxlDhdUvGv-plgqpMu0-GWiQ5Z4iu6obT7cHmQThZ900wG1LqQfolmdzY9VjgbqcLXKSRRPlaF5aco1EWfRKmxPlxcEjy6WD1SISUBimA3HlQZTX-0UwejuyhzTQtEhL8BeV1jpiJMYxRpDxc4DArCcNg3nBcsq00UTBlFg2HP0T3jQXu6QOzLvlJkDEGvj1yEyCTK916V4VH16N4_qgYSBTWNN-GzFzEC_mlqt0_4CjpQBjifOUa5hLhYGDEb-bSWfLf4aKow_TtauwB_6plOBXOk1rGBA5dLVg6AvkI2Be2pExCTeiBE3jsqjdluyjjA1-8-VKDolOa0PVfHethiAZybhBR0fdXlZo';
        return $this->withHeaders([
            'accept'        => ' application/vnd.sokradeo.v1+json',
            'Authorization' => sprintf('Bearer %s', $access_token),
        ]);

    }
}
