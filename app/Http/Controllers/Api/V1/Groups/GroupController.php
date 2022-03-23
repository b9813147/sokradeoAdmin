<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Services\GroupChannelService;
use App\Services\GroupService;
use App\Services\RatingService;
use App\Services\RecordService;
use App\Services\SemestersService;
use App\Services\UserService;
use App\Types\Cms\CmsType;
use App\Types\Record\RecordType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    protected $groupService;
    protected $userService;
    protected $groupChannelService;
    protected $recordService;
    /**
     * @var RatingService
     */
    protected $ratingService;
    /**
     * @var SemestersService
     */
    protected $semestersService;

    public function __construct(
        GroupService        $groupService,
        UserService         $userService,
        GroupChannelService $groupChannelService,
        RecordService       $recordService,
        RatingService       $ratingService,
        SemestersService    $semestersService

    )
    {
        $this->groupService        = $groupService;
        $this->userService         = $userService;
        $this->groupChannelService = $groupChannelService;
        $this->recordService       = $recordService;
        $this->ratingService       = $ratingService;
        $this->semestersService    = $semestersService;

    }

    public function index(): \Illuminate\Http\JsonResponse
    {

        return Response::json($this->groupService->getGroupList());
    }

//    /**
//     * 取得頻道名稱列表
//     *
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function getGroupAll(): \Illuminate\Http\JsonResponse
//    {
//        $groups = $this->groupService->getGroupList('*');
//
//        return Response::json($groups);
//    }

    /**
     * 活動頻道
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEventChannel(): \Illuminate\Http\JsonResponse
    {
        $eventChannel = $this->groupService->findWhere(['public' => 1])->sortDesc()->values()->all();

        return \response()->json($eventChannel, 200);

    }

    /**
     * groupId
     *
     * @param $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGroupByGroupId($group_id): \Illuminate\Http\JsonResponse
    {
        $groups = $this->groupService->getGroupById($group_id);

        return Response::json($groups);
    }

    /**
     * 頻道資訊
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $users = $this->groupService->getGroupInfo($id);

        return Response::json(new GroupResource($users));
    }

    /**
     * 檢查頻道使用者存不存在
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMemberGroupExist(Request $request): \Illuminate\Http\JsonResponse
    {
        $result = collect();
        foreach (array_filter(explode(PHP_EOL, $request->habook)) as $item) {
            // 排除特殊字元
            $user = trim(preg_replace("/[‘.,:;*?~`!@$%^& =)(<>{}]|\]|\[|\/|\\\|\"|\|/", '', $item));

            $userInfo = $this->userService->firstWhere(['habook' => $user]);

            if (empty($userInfo)) {
                $result->push($user . __('response.not_exist'));

            } else {
                $exist = $this->userService->getUser($userInfo->id)->groups()->where('group_id', $request->group_id)->exists();
                if (!$exist) {
                    $result->push($user . __('response.can_join'));

                }
                if ($exist) {
                    $result->push($user . __('response.exist'));
                }
            }
        }
        return Response::json(['message' => $result]);
    }

    /**
     * @param GroupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(GroupRequest $request)
    {
        $lang                         = $request->input('lang') ?? 'tw';
        $resources                    = $request->only('school_code', 'name', 'description', 'thumbnail', 'status', 'public', 'review_status', 'school_upload_status', 'public_note_status');
        $resources['status']          = 1;
        $resources['country_code']    = $this->getLang($lang);
        $channelResources             = $request->only('name', 'description', 'thumbnail');
        $channelResources['cms_type'] = CmsType::TbaVideo;
        $channelResources['public']   = 1;
        $channelResources['status']   = 1;
        $files                        = $request->file('thumbnail');
        if ($files) {
            // 副檔名
            $extension = $files->getClientOriginalExtension();
            // 檔案原始名稱
            $name = explode('.', $files->getClientOriginalName(), -1)[0];
            //修改名稱
            $name = 'thum.' . $extension;
            // 檔案上傳之前先寫 Group table
            $resources['thumbnail'] = $name;
            $groupInfo              = $this->groupService->create($resources);
            // 建立頻道
            $channelResources['thumbnail'] = $name;
            $groupInfo->channels()->create($channelResources);
            // 建立預設資料
            $this->createDefaultValue($lang, $groupInfo->id);
            $this->recordService->create(['type' => RecordType::CREATE, 'user_id' => auth()->id(), 'group_id' => $groupInfo->id]);
            $channel_id = GlobalPlatform::convertGroupIdToChannelId($groupInfo->id);
            // 建立檔案 檔名使用Group_id命名
            Storage::makeDirectory('public/group/' . $groupInfo->id);
            Storage::makeDirectory('public/group_channel/' . $channel_id);
            //圖片存擋
            Storage::putFileAs('public/group/' . $groupInfo->id, $files, $name);
            Storage::putFileAs('public/group_channel/' . $channel_id, $files, $name);

            return response()->json(['message' => 'success', 'status' => 200]);
        } else {
            $groupInfo = $this->groupService->create($resources);
            $groupInfo->channels()->create($channelResources);
            // 建立預設資料
            $this->createDefaultValue($request->input('lang'), $groupInfo->id);
            $this->recordService->create(['type' => RecordType::CREATE, 'user_id' => auth()->id(), 'group_id' => $groupInfo->id]);
            return response()->json(['message' => 'success', 'status' => 200]);
        }
    }


    /**
     * @param Request $request
     * @param int $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $group_id): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'school_code'          => 'string',
            'name'                 => 'string',
            'description'          => 'string',
            'thumbnail'            => 'file',
            'status'               => 'integer',
            'public'               => 'integer',
            'review_status'        => 'string',
            'public_note_status'   => 'integer',
            'event_data'           => 'array',
            'school_upload_status' => 'integer',
            'country_code'         => 'string',
            'groupId'              => 'integer'
        ]);
        $channelId = GlobalPlatform::convertGroupIdToChannelId($group_id);
        $files     = $request->file('thumbnail');

        try {
            // 操作記錄
            $this->recordService->create(['type' => RecordType::UPDATE, 'user_id' => auth()->id(), 'group_id' => $group_id]);
            // 更新活動內容
            if ($request->has('event_data')) {
                return $this->groupService->updateGroupEventData($group_id, json_encode($request->event_data)) ?
                    response()->json(['message' => '活動更新成功', 'status' => 200]) : response()->json(['message' => '活動更新失敗', 'status' => 404]);
            }
            if (!is_null($files)) {
                // 副檔名
                $extension = $files->getClientOriginalExtension();
                // 檔案原始名稱
                $name = explode('.', $files->getClientOriginalName(), -1)[0];
                //修改名稱
                $name = 'thum.' . $extension;
                // 檔案上傳之前先寫 Group table
                $this->groupService->updateGroupAndGroupChannel($request->all(), $files, $name);
                // 建立檔案 檔名使用Group_id命名
                Storage::makeDirectory('public/group/' . $group_id);
                Storage::makeDirectory('public/group_channel/' . $channelId);
                //圖片存擋:q
                Storage::putFileAs('public/group/' . $group_id, $files, $name);
                Storage::putFileAs('public/group_channel/' . $channelId, $files, $name);
                return response()->json(['message' => '更新成功', 'status' => 200]);
            } else {
                $this->groupService->updateGroupAndGroupChannel($request->all(), null, null);
                return response()->json(['message' => '更新成功', 'status' => 200]);
            }
        } catch (\Exception $exception) {
            return \response()->json(['message' => $exception->getMessage(), 'status' => 404], 200);
        }

    }

    /**
     * 取得國碼
     * @param $lang
     * @return int
     */
    protected function getLang($lang): int
    {
        switch ($lang) {
            case 'tw':
                $lang = 886;
                break;
            case 'cn':
                $lang = 86;
                break;
            default:
                $lang = 1;
        }
        return $lang;
    }

    /**
     * @param Request $request
     * @param $files
     * @param $name
     * @return bool|int
     */
    private function updateGroup($request, $files = null, $name = null)
    {
        if (!is_null($files)) {
            return $this->groupService->update($request->groupId, [
                'school_code'   => $request->school_code,
                'name'          => $request->name,
                'description'   => $request->description,
                'thumbnail'     => $name,
                'status'        => $request->status,
                'review_status' => $request->review_status,
                'public'        => $request->public,
            ]);

        }
        return $this->groupService->update($request->groupId, [
            'school_code'   => $request->school_code,
            'name'          => $request->name,
            'description'   => $request->description,
            'status'        => $request->status,
            'review_status' => $request->review_status,
            'public'        => $request->public,
        ]);
    }

    private function createDefaultValue(string $lang, int $group_id)
    {
        $group = $this->groupService->find($group_id);
        // 學科
        $groupSubjectFields = collect([
            'cn' => [
                ['subject' => '语文', 'alias' => '语文', 'subject_fields_id' => 1, 'order' => 1],
                ['subject' => '数学', 'alias' => '数学', 'subject_fields_id' => 2, 'order' => 2],
                ['subject' => '英语', 'alias' => '英语', 'subject_fields_id' => 7, 'order' => 3],
            ],
            'tw' => [
                ['subject' => '語文', 'alias' => '語文', 'subject_fields_id' => 1, 'order' => 1],
                ['subject' => '數學', 'alias' => '數學', 'subject_fields_id' => 2, 'order' => 2],
                ['subject' => '英語', 'alias' => '英語', 'subject_fields_id' => 1, 'order' => 3],
            ],
            'en' => [
                ['subject' => 'Language', 'alias' => 'Language', 'subject_fields_id' => 1, 'order' => 1],
                ['subject' => 'Math', 'alias' => 'Math', 'subject_fields_id' => 2, 'order' => 2],
                ['subject' => 'English', 'alias' => 'English', 'subject_fields_id' => 1, 'order' => 3],
            ]
        ]);
        // 語系
        $locales_ids = collect([37, 40, 65]);
        // 教研
        $ratings = collect([
            'cn' => [
                ['type' => 0, 'name' => '教研'],
                ['type' => 1, 'name' => '佳作'],
                ['type' => 2, 'name' => '二等'],
                ['type' => 3, 'name' => '一等'],
                ['type' => 4, 'name' => '优等'],
            ],
            'tw' => [
                ['type' => 0, 'name' => '教研'],
                ['type' => 1, 'name' => '佳作'],
                ['type' => 2, 'name' => '二等'],
                ['type' => 3, 'name' => '一等'],
                ['type' => 4, 'name' => '優等'],
            ],
            'en' => [
                ['type' => 0, 'name' => 'Study'],
                ['type' => 1, 'name' => 'Good'],
                ['type' => 2, 'name' => 'Great'],
                ['type' => 3, 'name' => 'Perfect'],
                ['type' => 4, 'name' => 'Excellent'],
            ]
        ]);
        // 學期
        $semesters = collect([
            ['year' => Carbon::now()->year, 'month' => '8', 'day' => '1'],
            ['year' => Carbon::now()->year + 1, 'month' => '2', 'day' => '1']
        ]);

        // 學科
        $groupSubjectFields->each(function ($v, $k) use ($lang, $group) {
            if ($k === $lang) {
                collect($v)->each(function ($v) use ($group) {
                    $group->groupSubjectFields()->create($v);
                });
            }
        });
        // 教研
        $ratings->each(function ($v, $k) use ($lang, $group) {
            if ($k === $lang) {
                collect($v)->each(function ($v) use ($group) {
                    $group->ratings()->create($v);
                });
            }
        });
        // 語系
        $locales_ids->each(function ($v) use ($group) {
            $group->groupLangs()->create(['name' => $group->name, 'description' => $group->description, 'locales_id' => $v,]);
        });
        // 學期
        $semesters->each(function ($v) use ($group) {
            $group->semesters()->create($v);
        });
    }

    /**
     * 加入頻道
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function join(Request $request): \Illuminate\Http\JsonResponse
    {
        \Log::info('Join', $request->all());
        $data   = collect($request->all());
        $result = $this->groupService->join($data);

        return $this->setStatus($result['status'])->respond($result['message']);
    }

    /**
     * 更新使用者身份
     * @param Request $request
     * @param $abbr
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateJoin(Request $request, $abbr): \Illuminate\Http\JsonResponse
    {
        $data   = collect($request->all());
        $result = $this->groupService->updateJoin($abbr, $data);

        return $this->setStatus($result['status'])->respond($result['message']);
    }

    /**
     * 刪除頻道使用者
     *
     * @param Request $request
     * @param string $abbr
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeJoin(Request $request, string $abbr): \Illuminate\Http\JsonResponse
    {
        $teamModel_ids = $request->all();
        $result        = $this->groupService->removeJoin($abbr, $teamModel_ids);

        return $this->setStatus($result['status'])->respond($result['message']);
    }
}
