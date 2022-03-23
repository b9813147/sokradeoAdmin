<?php

namespace App\Http\Controllers\Api\V1\Events;

use App\Http\Controllers\Api\V1\Controller;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\GroupSubjectField;
use App\Models\GroupUser;
use App\Models\Rating;
use App\Models\User;
use App\Types\Cms\CmsType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * 創建活動頻道
     *
     * @param Request $request
     * @return mixed
     */
    public function addEventChannel(Request $request)
    {
        $school_code = $request->school_code;
        $name        = $request->name;

        $existsGroup = Group::query()->select('id')->where('school_code', $school_code)->exists();

        if ($existsGroup) {
            return $this->fail(['message' => 'School Code is Exist']);
        }

        $group_id = Group::query()->create([
            'school_code'        => $school_code,
            'name'               => $name,
            'status'             => 1,
            'public'             => 1,
            'review_status'      => 1,
            'abbr'               => $school_code,
            'notify_status'      => 0,
            'public_note_status' => 0,
            'country_code'       => 86,
        ])->id;
        // 語系
        $locales_id = collect([37, 40, 65]);
        $locales_id->each(function ($q) use ($group_id, $name) {
            Group::query()->findOrFail($group_id)->groupLangs()->create([
                'name'       => $name,
                'locales_id' => $q
            ]);
        });

        // 預設學科
        $defaultSubjects = collect([
            [
                'subject'           => '语文',
                'alias'             => '语文',
                'groups_id'         => $group_id,
                'subject_fields_id' => 1
            ],
            [
                'subject'           => '数学',
                'alias'             => '数学',
                'groups_id'         => $group_id,
                'subject_fields_id' => 2
            ],
            [
                'subject'           => '英文',
                'alias'             => '英文',
                'groups_id'         => $group_id,
                'subject_fields_id' => 7
            ],
        ]);
        // 學科新增
        $defaultSubjects->each(function ($defaultSubject) {
            GroupSubjectField::query()->create($defaultSubject);
        });
        // 預設分類
        $defaultRatings = collect([
            [
                'groups_id' => $group_id,
                'type'      => 1,
                'name'      => '教研'
            ],
            [
                'groups_id' => $group_id,
                'type'      => 2,
                'name'      => '佳作'
            ],
            [
                'groups_id' => $group_id,
                'type'      => 3,
                'name'      => '二等'
            ],
            [
                'groups_id' => $group_id,
                'type'      => 4,
                'name'      => '一等'
            ],
            [
                'groups_id' => $group_id,
                'type'      => 5,
                'name'      => '优等'
            ]
        ]);
        $defaultRatings->each(function ($defaultRating) {
            Rating::query()->create($defaultRating);
        });


        GroupChannel::query()->create([
            'group_id' => $group_id,
            'cms_type' => CmsType::TbaVideo,
            'name'     => $name,
            'status'   => 1,
            'public'   => 0,
        ]);


        return $this->success(['message' => 'success']);
    }

    /**
     * 成員加入活動頻道 支援批次加入
     *
     * @param Request $request
     * @return mixed|string
     */
    public function JoinEventUserByChannels(Request $request)
    {
        $userData = $request->userData;

        if (!is_array($userData)) {
            return $this->fail(['message' => 'userData Invalid format']);
        }

        $collectionUserData = collect($userData);
        $users              = collect();

        // 檢查teamModelId 是否有綁定 user 如果沒有 則建立user
        $collectionUserData->each(function ($userData) use ($users) {
            $objUserData = (object)$userData;

            if (!empty($objUserData->teamModeId) && !empty($objUserData->member_duty) && !empty($objUserData->name)) {
                $user = User::query()->where('habook', $objUserData->teamModeId)->first();

                if ($user instanceof User) {
                    $user->member_duty = $objUserData->member_duty;
                    $user->school_code = $objUserData->school_code;
                    $users->push($user);
                }

                if (!$user instanceof User) {
                    $user              = User::query()->create([
                        'habook' => $objUserData->teamModeId,
                        'name'   => $objUserData->name,
                    ]);
                    $user->member_duty = $objUserData->member_duty;
                    $user->school_code = $objUserData->school_code;
                    $users->push($user);
                }
            }
        });

        // 加入頻道
        foreach ($users as $user) {
            if ($user instanceof User) {
                if ($user->school_code) {
                    foreach ($user->school_code as $school_code) {
                        $group_id = Group::query()->select('id')->where('public', 1)->where('school_code', $school_code)->first();
                        if (!$group_id instanceof Group) {
                            return $this->fail(['message' => "$school_code is not event channel"]);
                        }
                        if ($group_id instanceof Group) {
                            GroupUser::query()->updateOrInsert([
                                'group_id' => $group_id->id,
                                'user_id'  => $user->id,
                            ], [
                                'member_status' => 1,
                                'member_duty'   => $user->member_duty,
                                'created_at'    => Carbon::now()->format('Y-m-d h:m:s'),
                                'updated_at'    => Carbon::now()->format('Y-m-d h:m:s')
                            ]);
                        }
                    }
                }
            }
        }
        return $this->success(['message' => 'success']);
    }
}
