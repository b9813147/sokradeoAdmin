<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Helpers\Code\ImageUploadHandler;
use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\GroupChannelContentCollection;
use App\Models\DistrictChannelContent;
use App\Models\TbaStatistic;
use App\Models\TbaStatisticLog;
use App\Services\AnnexService;
use App\Services\GroupChannelContentService;
use App\Services\RecordService;
use App\Types\Record\RecordType;
use App\Types\Tba\AnnexType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mockery\Exception;

class ChannelContentController extends Controller
{
    use ImageUploadHandler;

    protected $groupChannelContentService;
    protected $annexService;
    protected $recordService;

    public function __construct(GroupChannelContentService $groupChannelContentService, AnnexService $annexService, RecordService $recordService)
    {
        $this->groupChannelContentService = $groupChannelContentService;
        $this->annexService               = $annexService;
        $this->recordService              = $recordService;
    }

    /**
     *  textbook_practice (C 分數) 55
     *  instructional_design (教學設計)  56
     *  teaching_process (教學過程)  57
     *  teaching_effect (教學效果)  58
     *  technology_application (技術應用) 59
     *  fusion_Innovation (融合創新)  60
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editScore(Request $request)
    {
        $data = (object)$request->input('data');
        try {
            TbaStatistic::query()->updateOrInsert(['tba_id' => $data->tba_id, 'type' => 55],
                [
                    'created_at' => Carbon::now()->format('Y-m-d h:m:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d h:m:s'),
                    'idx'        => $data->textbook_practice
                ]);
            TbaStatistic::query()->updateOrInsert(['tba_id' => $data->tba_id, 'type' => 56],
                [
                    'created_at' => Carbon::now()->format('Y-m-d h:m:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d h:m:s'),
                    'idx'        => (int)$data->instructional_design * 0.01
                ]);
            TbaStatistic::query()->updateOrInsert(['tba_id' => $data->tba_id, 'type' => 57],
                [
                    'created_at' => Carbon::now()->format('Y-m-d h:m:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d h:m:s'),
                    'idx'        => (int)$data->teaching_process * 0.01
                ]);

            TbaStatistic::query()->updateOrInsert(['tba_id' => $data->tba_id, 'type' => 58],
                [
                    'created_at' => Carbon::now()->format('Y-m-d h:m:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d h:m:s'),
                    'idx'        => (int)$data->teaching_effect * 0.01
                ]);
            TbaStatistic::query()->updateOrInsert(['tba_id' => $data->tba_id, 'type' => 59],
                [
                    'created_at' => Carbon::now()->format('Y-m-d h:m:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d h:m:s'),
                    'idx'        => (int)$data->technology_application * 0.01
                ]);
            TbaStatistic::query()->updateOrInsert(['tba_id' => $data->tba_id, 'type' => 60],
                [
                    'created_at' => Carbon::now()->format('Y-m-d h:m:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d h:m:s'),
                    'idx'        => (int)$data->fusion_Innovation * 0.01
                ]);

            TbaStatisticLog::query()->updateOrCreate(['tba_id' => $data->tba_id], ['user_id' => $data->user_id]);

            return response()->json(['message' => 'success', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => $e->getCode()]);
        }
    }

    public function show($group_id)
    {
        $groupChannelContentInfo = $this->groupChannelContentService->getChannelContentList($group_id);

        return response()->json(new GroupChannelContentCollection($groupChannelContentInfo));
    }

    /**
     * @param Request $request
     * @param int $group_id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, int $group_id)
    {
        $files = $request->file('thumbnail');

        if ($request->file('HiTeachNote')) {
            $this->annexService->saveFile($request->id, $request->user_id, $request->file('HiTeachNote'), AnnexType::HiTeachNote);
        }
        if ($request->file('LessonPlan')) {
            $this->annexService->saveFile($request->id, $request->user_id, $request->file('LessonPlan'), AnnexType::LessonPlan);
        }
        if ($request->file('Material')) {
            $this->annexService->saveFile($request->id, $request->user_id, $request->file('Material'), AnnexType::Material);
        }

        if ($files) {
            $fileName = $this->imageSave($files, 'tba', $request->id);
            $this->groupChannelContentService->updateGroupChannelContentAndTba($request, $group_id, $fileName);
            // 操作紀錄
            $this->recordService->create(['type' => RecordType::UPDATE, 'user_id' => auth()->id(), 'tba_id' => $request->id, 'group_id' => $group_id, 'rating_id' => $request->rating, 'group_subject_field_id' => $request->subject]);
            return response()->json(['message' => 'success', 'status' => 200]);
        } else {
            $this->groupChannelContentService->updateGroupChannelContentAndTba($request, $group_id);
            // 操作紀錄
            $this->recordService->create(['type' => RecordType::UPDATE, 'user_id' => auth()->id(), 'tba_id' => $request->id, 'group_id' => $group_id, 'rating_id' => $request->rating, 'group_subject_field_id' => $request->subject]);
            return response()->json(['message' => 'success', 'status' => 200]);
        }
    }

    public function destroy(Request $request, $group_id)
    {
        $this->groupChannelContentService->deleteGroupChannelContent($group_id, $request->id);
        DistrictChannelContent::query()->where('groups_id', $group_id)->where('content_id', $request->id)->delete();
        // 操作紀錄
        $this->recordService->create([
            'type'                   => RecordType::DELETE, 'user_id' => auth()->id(),
            'tba_id'                 => $request->id,
            'group_id'               => $group_id,
            'rating_id'              => $request->rating->value ?? 0,
            'group_subject_field_id' => $request->alias->value ?? 0
        ]);

        return response()->json(['message' => 'success', 'status' => 200]);
    }

    public function lessonPlayer(Request $request, $group_id)
    {
        $channelId = GlobalPlatform::convertGroupIdToChannelId($group_id);
        $tbaId     = (object)$request->data;

        $url = getenv('SOKRADEO_URL') . "/exhibition/tbavideo#/content/$tbaId->id?groupIds=$group_id&channelId=$channelId";

        return response()->json(['url' => $url, 'status' => 200]);
    }

    public function store(Request $request, $group_id)
    {
        $originalData = (object)$request->input('params');
        $this->groupChannelContentService->shareVideo($group_id, $originalData);
        $this->status = 201;
        return $this->success('success', 201);
    }
}

