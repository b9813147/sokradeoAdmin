<?php

namespace App\Http\Controllers\Api\V1\Events;

use App\Http\Controllers\Api\V1\Controller;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\GroupUser;
use App\Models\User;
use App\Types\Cms\CmsType;
use Illuminate\Http\Request;

class SchoolChannelController extends Controller
{
    /**
     * 創建活動頻道
     *
     * @param Request $request
     * @return mixed
     */
    public function addSchoolChannel(Request $request)
    {
        $school_code = $request->school_code;
        $name        = $request->name;
        $userData    = $request->userData;

        $existsGroup = Group::query()->select('id')->where('school_code', $school_code)->exists();

        if ($existsGroup) {
            return $this->fail(['message' => 'School Code is Exist']);
        }

        $group_id = Group::query()->create([
            'school_code'   => $school_code,
            'name'          => $name,
            'status'        => 1,
            'public'        => 1,
            'review_status' => 0,
        ])->id;

        GroupChannel::query()->create([
            'group_id' => $group_id,
            'cms_type' => CmsType::TbaVideo,
            'name'     => $name,
            'status'   => 1,
            'public'   => 1,
        ]);

        $this->joinSchoolUserByChannels($userData, $group_id);

        return $this->success(['message' => 'success']);
    }

    /**
     * 成員加入活動頻道 支援批次加入
     *
     * @param array $userData
     * @param int $group_id
     * @return mixed|string
     */
    private function joinSchoolUserByChannels($userData = [], $group_id)
    {
//        $userData = $request->userData;

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
                    $users->push($user);
                }

                if (!$user instanceof User) {
                    $user              = User::query()->create([
                        'habook' => $objUserData->teamModeId,
                        'name'   => $objUserData->name,
                    ]);
                    $user->member_duty = $objUserData->member_duty;
                    $users->push($user);
                }
            }
        });

        // 加入頻道
        foreach ($users as $user) {
            if ($user instanceof User) {
                GroupUser::query()->updateOrCreate([
                    'group_id'      => $group_id,
                    'user_id'       => $user->id,
                    'member_status' => 1,
                    'member_duty'   => $user->member_duty,
                ]);
            }
        }
    }
}
