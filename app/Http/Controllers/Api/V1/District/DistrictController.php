<?php

namespace App\Http\Controllers\Api\V1\District;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\DistrictResource;
use App\Http\Resources\DistrictsCollection;
use App\Libraries\Lang\Lang;
use App\Models\DistrictGroupSubject;
use App\Models\GroupChannelContent;
use App\Services\DistrictService;
use App\Services\GroupService;
use App\Services\RecordService;
use App\Types\Record\RecordType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

class DistrictController extends Controller
{
    use Lang;

    /**
     * @var DistrictService
     */
    protected $districtService;
    protected $recordService;
    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * DistrictController constructor.
     * @param DistrictService $districtService
     * @param RecordService $recordService
     * @param GroupService $groupService
     */
    public function __construct(DistrictService $districtService, RecordService $recordService, GroupService $groupService)
    {
        $this->districtService = $districtService;
        $this->recordService   = $recordService;
        $this->groupService    = $groupService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): object
    {
        $convertByLangStringForId = Lang::getConvertByLangStringForId();
        try {
            $districtInfo = $this->districtService->getDistrictInfoAll($convertByLangStringForId);
            return $this->success(new DistrictsCollection($districtInfo));
        } catch (Exception $exception) {
            return $this->fail(['message' => $exception->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $resource  = $request->only('abbr', 'school_code', 'status', 'thumbnail');
        $group_ids = collect(explode(',', $request->input('group_ids')));
        try {
            if ($request->hasFile('thumbnail')) {
                $files = $request->file('thumbnail');
                // 副檔名
                $extension = $files->getClientOriginalExtension();
                // 檔案原始名稱
                $name = explode('.', $files->getClientOriginalName(), -1)[0];
                //修改名稱
                $name                  = 'thum.' . $extension;
                $resource['thumbnail'] = $name;
                // 檔案上傳之前先寫 Group table
                $districtInfo = $this->districtService->create($resource);
                // 建立預設資料
                $this->createDefaultValue($request->input('lang'), $districtInfo->id, $request->only('name', 'description'));
                // 建立檔案 檔名使用district_id命名
                Storage::makeDirectory('public/district/' . $districtInfo->id);
                //圖片存擋
                Storage::putFileAs('public/district/' . $districtInfo->id, $files, $name);

                if ($request->has('group_ids')) {
                    $this->districtService->find($districtInfo->id)->groups()->attach($group_ids);
                    $this->initializeDistrict($districtInfo->id);
                }

                return $this->success(['message' => 'success']);
            }

            $districtInfo = $this->districtService->create($resource);
            // 建立預設資料
            $this->createDefaultValue($request->input('lang'), $districtInfo->id, $request->only('name', 'description'));
            if ($request->has('group_ids')) {
                $this->districtService->find($districtInfo->id)->groups()->attach($group_ids);
                $this->initializeDistrict($districtInfo->id);
            }

            return $this->success(['message' => 'success']);

        } catch (\Exception $exception) {
            return $this->fail(['message' => $exception->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $districtId
     * @return \Illuminate\Http\Response
     */
    public function show(int $districtId)
    {
        $convertByLangStringForId = Lang::getConvertByLangStringForId();

        try {
            $districtInfo = $this->districtService->getDistrictInfo($districtId, $convertByLangStringForId);

            return $this->success(new DistrictResource($districtInfo));
        } catch (Exception $exception) {
            return $this->fail(['message' => $exception->getMessage()]);
        }

    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $districtId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $districtId): \Illuminate\Http\JsonResponse
    {
        $resource = $request->only('abbr', 'school_code', 'status', 'thumbnail');

        $group_ids = is_string($request->input('group_ids')) ? explode(',', $request->input('group_ids')) : $request->input('group_ids');
        $group_ids = collect($group_ids);

        try {
            $files = $request->file('thumbnail');
            // 操作記錄
            $this->recordService->create([
                'type'        => RecordType::UPDATE,
                'user_id'     => auth()->id(),
                'district_id' => $districtId,
            ]);

            // 處理學區頻道增減
            if ($request->has('group_ids')) {
                $diff_add_Ids    = $group_ids->diff($this->districtService->find($districtId)->groups()->get()->pluck('id'))->toArray();
                $diff_delete_Ids = $this->districtService->find($districtId)->groups()->get()->pluck('id')->diff($group_ids)->toArray();
                $this->districtService->find($districtId)->groups()->attach($diff_add_Ids);
                $this->districtService->find($districtId)->groups()->detach($diff_delete_Ids);
                $this->initializeDistrict($districtId);
            }
            // 更新多語系
            if ($request->has('description')) {
                $this->districtService->find($districtId)->districtLangs->each(function ($v) use ($request, $districtId) {
                    $v->update($request->only('name', 'description'));
                });
            }

            if (!is_null($files)) {
                // 副檔名
                $extension = $files->getClientOriginalExtension();
                // 檔案原始名稱
                $name = explode('.', $files->getClientOriginalName(), -1)[0];
                //修改名稱
                $name                  = 'thum.' . $extension;
                $resource['thumbnail'] = $name;
                // 檔案上傳之前先寫 Group table
                $this->districtService->updateBy('id', $districtId, $resource);
                // 建立檔案 檔名使用Group_id命名
                Storage::makeDirectory('public/district/' . $districtId);
                //圖片存擋
                Storage::putFileAs('public/district/' . $districtId, $files, $name);

                return $this->success(['message' => 'success']);
            }

            $this->districtService->updateBy('id', $districtId, $resource);
            return $this->success(['message' => 'success']);

        } catch (Exception $exception) {
            return $this->fail(['message' => $exception->getMessage()]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param string $lang
     * @param int $district_id
     * @param array $districtLang
     */
    private function createDefaultValue(string $lang, int $district_id, array $districtLang)
    {
        $districtInfo = $this->districtService->find($district_id);
        // 語系
        $locales_ids = collect([37, 40, 65]);
        // 教研
        $ratings = collect([
            'cn' => [
                ['type' => '1', 'name' => '教研'],
                ['type' => '2', 'name' => '佳作'],
                ['type' => '3', 'name' => '二等'],
                ['type' => '4', 'name' => '一等'],
                ['type' => '5', 'name' => '优等'],
            ],
            'tw' => [
                ['type' => '1', 'name' => '教研'],
                ['type' => '2', 'name' => '佳作'],
                ['type' => '3', 'name' => '二等'],
                ['type' => '4', 'name' => '一等'],
                ['type' => '5', 'name' => '優等'],
            ],
            'en' => [
                ['type' => '1', 'name' => 'Study'],
                ['type' => '2', 'name' => 'Good'],
                ['type' => '3', 'name' => 'Great'],
                ['type' => '4', 'name' => 'Perfect'],
                ['type' => '5', 'name' => 'Excellent'],
            ]
        ]);

        // 教研
        $ratings->each(function ($v, $k) use ($lang, $districtInfo) {
            if ($k === $lang) {
                collect($v)->each(function ($v) use ($districtInfo) {
                    $districtInfo->ratings()->create($v);
                });
            }
        });
        // 語系
        $locales_ids->each(function ($v) use ($districtLang, $districtInfo) {
            $districtInfo->districtLang()->create(['name' => $districtLang['name'], 'description' => $districtLang['description'], 'locales_id' => $v,]);
        });
    }

    /**
     * 建立學區 district_channel_content 及同步相關資料
     * @param int $district_id
     */
    private function initializeDistrict(int $district_id)
    {
        $district = $this->districtService->find($district_id);
        $groupIds = [];
        // 找出學區 $groupIds
        $groupIds = $district->groups->map(function ($q) {
            return $q->id;
        })->toArray();

        // 建立學區
        $rating = $district->ratings->first();
        GroupChannelContent::query()->whereIn('group_id', $groupIds)->where(['content_status' => 1, 'content_public' => 1])->get()->each(function ($q) use ($district, $rating) {
            $district->districtChannelContents()->updateOrCreate([
                'content_id' => $q->content_id,
                'groups_id'  => $q->group_id,
            ], [
                    'ratings_id'              => $rating->id,
                    'grades_id'               => $q->grades_id,
                    'group_subject_fields_id' => $q->group_subject_fields_id,
                ]
            );
        });

        // 建立學區學科
        $this->groupService->findWhereIn('id', $groupIds)->each(function ($v) use ($district) {
            $v->groupSubjectFields()->groupBy('subject')->get()->each(function ($v, $k) use ($district) {
                $district->districtSubjects()->updateOrCreate([
                    'subject' => $v->subject,
                    'alias'   => $v->alias,
                ], [
                    'subject' => $v->subject,
                    'alias'   => $v->alias,
                    'order'   => $k + 1,
                ]);
            });
        });
        // 建立 學區與頻道學科合併
        $this->groupService->findWhereIn('id', $groupIds)->each(function ($v) use ($district) {
            $v->groupSubjectFields()->get()->each(function ($v) use ($district) {
                DistrictGroupSubject::query()->updateOrInsert([
                    'group_subject_fields_id' => $v->id,
                ], [
                    'group_subject_fields_id' => $v->id,
                    'district_subjects_id'    => $district->districtSubjects()->where('subject', $v->subject)->pluck('id')->first() ?? null,
                ]);
            });
        });
    }

}
