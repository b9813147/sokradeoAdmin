<?php

namespace Tests\Feature;

//use App\Repositories\Tba\AnnexRepository as TbaAnnexRepository;
use App\Helpers\Code\ImageUploadHandler;
use App\Models\File;
use App\Models\Resource;
use App\Models\TbaAnnex;
use App\Models\User;
use App\Repositories\AnnexRepository;
use App\Repositories\TbaAnnexRepository;
use App\Types\Src\SrcType;
use App\Types\Src\VodType;
use App\Types\Tba\AnnexType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AnnexTest extends TestCase
{
    use ImageUploadHandler;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        dd(Storage::exists('app/file/1'));
//        $TbaAnnexRepository = new TbaAnnexRepository(new TbaAnnex);
//
//        $data = $TbaAnnexRepository->firstWhere(['type' => AnnexType::HiTeachNote, 'tba_id' => 1]);
//        dd($data);
//        $name = "filename.xlsx";
//        $file = '';
//        $ext  = $file->getClientOriginalExtension;
//        $name  = explode('.',$file->getClientOriginalName)[0];
        // todo 上傳檔案 name usage tba_annexes_id
        // tbaId
        $request = (object)[
            'type'    => AnnexType::LessonPlan,
            'id'      => 1,
            'user_id' => 948,
            'name'    => 'testName'
        ];
        if ($request->type === AnnexType::HiTeachNote) {
            $tbaAnnexes = TbaAnnex::query()
                ->where(['type' => AnnexType::HiTeachNote, 'tba_id' => $request->id])
                ->first();

            if (collect($tbaAnnexes)->isEmpty()) {
//                $resource = $tbaAnnexes->resources->create([
//                    'user_id'  => $request->user_id,
//                    'src_type' => SrcType::File,
//                    'name'     => $request->name
//                ]);
//                dd($resource);
//                $tbaAnnexes->create([
//                    'tba_iid' => $request->id,
//                ]);

            }
        }

        if ($request->type === AnnexType::LessonPlan) {
            $tbaAnnexes = TbaAnnex::query()
                ->where(['type' => AnnexType::LessonPlan, 'tba_id' => $request->id])
                ->first();
            if (collect($tbaAnnexes)->isEmpty()) {
                $resource = Resource::query()->create([
                    'user_id'  => $request->user_id,
                    'src_type' => SrcType::File,
                    'name'     => $request->name,
                    'status'   => 1
                ]);
                $resource->file()->create([
                        'name' => $request->name,
                        'ext'  => 'pdf',
                    ]
                );

                TbaAnnex::query()->create([
                    'tba_id'      => $request->id,
                    'resource_id' => $resource->id,
                    'type'        => AnnexType::LessonPlan,
                ]);
                dd();
                // file storage $tbaAnnexId
//                return $file->move(storage_path("app/file/"), $fileId);
            }
            $tbaAnnexes->resource_id;
            Resource::query()->where('id', $tbaAnnexes->resource_id)
                ->update([
                    'name' => $request->name,
                ]);
            File::query()->where('resource_id', $tbaAnnexes->resource_id)
                ->update([
                    'name' => $request->name,
                    'ext'  => $request->ext,
                ]);
            $fileId = File::query()->where('resource_id', $tbaAnnexes->resource_id)->pluck('id')->first();
            return $file->move(storage_path("app/file/"), $fileId);

        }
        if ($request->type === AnnexType::HiTeachNote) {
//            dump($tbaAnnexes->type === AnnexType::Material);
        }

//        dd($tbaAnnexes);
//        dd($annexes->toArray());
        $this->assertTrue(true);
//        $response = $this->get('/');
//
//        $response->assertStatus(200);
    }

    public function testMember()
    {
        $first = User::first();
        dd($first);
        dd(1);
    }

}
