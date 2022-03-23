<?php

namespace Tests\Feature;

use App\Helpers\Custom\GlobalPlatform;
use App\Http\Resources\RatingCollection;
use App\Libraries\Lang\Lang;
use App\Models\Grade;
use App\Models\GradeLang;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\GroupChannelContent;
use App\Models\GroupSubjectField;
use App\Models\GroupUser;
use App\Models\Rating;
use App\Models\Tba;
use App\Models\User;
use App\Models\Video;
use App\Services\GroupService;
use App\Services\GroupSubjectFieldsService;
use App\Services\RatingService;
use App\Types\Cms\CmsType;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\FactoryBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpParser\Builder;
use Tests\TestCase;

class GroupChannelContentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /*public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testGroupChannelContentList()
    {

        $group_id = 7;
        // 影片篩選
        $content = GroupChannelContent::query()->where(['group_id' => $group_id, 'content_type' => CmsType::Tba]);
        // 取出影片ID
        $tbaId = $content->pluck('content_id');

        $tbas     = Tba::query()
            ->with([
                'educationalStage' => function ($q) {
                    $q->select('educational_stages.id', 'educational_stages.type', 'educational_stages.total_grade');
                }
            ])
            ->whereIn('id', $tbaId)
            ->get();
        $contents = $content->select('content_id', 'content_status', 'content_public')->get();
//        dd($tbas);
//        dd($tbas->toArray());


        // 無效不顯示  invalid (0, 0) 1
        // 頻道內觀摩  valid (1, 0)  2
        // 全平台分享  share (1, 1) 3
        // 尚待審核中  pending (2, 0) 4
        $tbas->each(function ($tba) use ($contents) {
            $contents->each(function ($content) use ($tba) {
                if ($tba->id === $content->content_id) {
                    if ($content->content_status === 1 && $content->content_public === 1) {
                        return $tba->status = 3;
                    } elseif ($content->content_status === 1 && $content->content_public === 0) {
                        return $tba->status = 2;
                    } elseif ($content->content_status === 2 && $content->content_public === 0) {
                        return $tba->status = 4;
                    }
                    return $tba->status = 1;
                }
            });
        });

        dd($tbas->toArray());
    }

    public function testUpdateGroupChannelContent()
    {
        $group_id = 7;
        $tbaId   = 65;
        $request = '';
//        1 0
        $status = $this->status(1);
        // Update GroupChannelContent Status
        $groupContent = GroupChannelContent::query()->where([
            'group_id'   => $group_id,
            'content_id' => $tbaId,
        ])->update($status);

        $tba = Tba::query()->findOrfail($tbaId);

        $tba->update([
            'name'                 => $request->name,
            'thumbnail'            => $request->thumbnail,
            'subject'              => $request->subject,
            'grade'                => $request->grade,
            'educational_stage_id' => $request->educational_stage_id,
            'description'          => $request->description
        ]);
        dd($tba->toArray());


        dd($groupContent);


    }*/
    protected $allowed_ext = ["png", "jpg", "gif", 'jpeg'];

    public function testFile()
    {
        dd(storage_path('app/public'));
        $folder_path = 'tba';
        $folder_name = 'test';
        // 建立指定位置文件夾
        \Storage::makeDirectory("public/$folder_path/" . $folder_name);
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

    }

    // 無效不顯示  invalid (0, 0) 1
    // 頻道內觀摩  valid (1, 0)  2
    // 全平台分享  share (1, 1) 3
    // 尚待審核中  pending (2, 0) 4
    public function status($statusId)
    {
        switch ($statusId) {
            case 1:
                $status = [
                    'content_status' => 0,
                    'content_public' => 0
                ];
                break;
            case 2:
                $status = [
                    'content_status' => 1,
                    'content_public' => 0
                ];
                break;
            case 3:
                $status = [
                    'content_status' => 1,
                    'content_public' => 1
                ];
                break;
            default:
                $status = [
                    'content_status' => 2,
                    'content_public' => 0
                ];
                break;
        }
        return $status;
    }

    public function testDataCount()
    {
        $tbaModel = GroupChannel::query()->with([
            'tbas' => function ($q) {
                $q->with([
                    'tbaPlaylistTracks' => function ($q) {
                        $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                            ->orderBy('tba_playlist_tracks.list_order');
                    },
                    'tbaStatistics'     => function ($q) {
                        $q->selectRaw('MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P,tba_statistics.tba_id,MAX(CASE WHEN type = 55 THEN CAST(idx AS signed) ELSE 0 END) AS C')
                            ->groupBy('tba_statistics.tba_id');
                    },
                ]);
            }
        ])->find(3);

        $ratings  = $tbaModel->tbas->groupBy('pivot.ratings_id');
        $grades   = $tbaModel->tbas->groupBy('pivot.grades_id');
        $subjects = $tbaModel->tbas->groupBy('pivot.group_subject_fields_id');


        $result                = (object)[];
        $publicVideo           = 1;
        $doubleGreenLightTotal = 1;
        $videoTotal            = 1;
        $hits                  = 0;

        $ratingsModel = Rating::query()->select('id', 'name')->where('groups_id', $tbaModel->group_id)->get();

        // 教研分類
        $ratings->each(function ($v, $k) use (&$result, $ratingsModel) {
            $result->ratings[] = [
                'name'  => $ratingsModel->firstWhere('id', $k)->name,
                'id'    => $k,
                'total' => count($v),
            ];
        });
        // 年級統計
        $locales_id = Lang::getConvertByLangStringForId();
        $grades->each(function ($v, $k) use (&$result, $locales_id) {
            $result->grades[] = [
                'name'  => !empty(Grade::query()->with('gradeLang')->where('id', $k)->first()->gradeLang)
                    ? Grade::query()->with('gradeLang')->where('id', $k)->first()->gradeLang()->where('locales_id', $locales_id)->pluck('name')->first()
                    : 'other',
                'id'    => $k ?: '13',
                'total' => count($v),
            ];
        });
        $result->grades = array_values(Arr::sort($result->grades, function ($value) {
            return $value['id'];
        }));

        $groupSubjectFieldModel = GroupSubjectField::query()->where('groups_id', $tbaModel->group_id)->select('groups_id', 'id', 'alias', 'order')->get();
        // 學科統計
        $subjects->each(function ($v, $k) use (&$result, $locales_id, $groupSubjectFieldModel) {
            $result->subjects[] = [
                'name'  => !empty($groupSubjectFieldModel->firstWhere('id', $k)->alias) ? $groupSubjectFieldModel->firstWhere('id', $k)->alias : 'other',
                'id'    => $k ?: 'other',
                'order' => !empty($groupSubjectFieldModel->firstWhere('id', $k)->order) ? $groupSubjectFieldModel->firstWhere('id', $k)->order : 9999,
                'total' => count($v),
            ];
        });
        $result->subjects = array_values(Arr::sort($result->subjects, function ($value) {
            return $value['order'];
        }));


        $tbaModel->tbas->each(function ($q) use (&$result, &$publicVideo, &$doubleGreenLightTotal, &$videoTotal, &$hits) {
            // 公開影片數
            if ($q->pivot->content_public === 1 && $q->pivot->content_status === 1) {
                $result->public_video = $publicVideo++;
            }
            // 影片總數 & 點閱總數
            if (($q->pivot->content_status === 1 || $q->pivot->content_status === 2) || ($q->pivot->content_pulic === 1 || $q->pivot->content_pulic === 0)) {
                // 雙綠燈總數
                if ($q->tbaStatistics->isNotEmpty()) {
                    if ((int)$q->tbaStatistics->first()->T >= 70 && (int)$q->tbaStatistics->first()->P >= 70) {
                        $result->double_green_light_total = $doubleGreenLightTotal++;
                    }
                }
                $result->video_total = $videoTotal++;
                $result->his_total   = $hits += $q->hits;
            }
        });
        $resultInit  = [
            'public_video'             => 0,
            'video_total'              => 0,
            'double_green_light_total' => 0,
            'his_total'                => 0,
        ];
        $array_merge = array_merge($resultInit, (array)$result);
        dd($array_merge);
//        $videoTotal            = $tbaModel->tbas->filter(function ($q) {
//            if (($q->pivot->content_status === 1 || $q->pivot->content_status === 2) || ($q->pivot->content_pulic === 1 || $q->pivot->content_pulic === 0)) {
//                return $q;
//            }
//        });
//        $publicVideo           = $tbas->filter(function ($q) {
//            if ($q->pivot->content_public === 1 && $q->pivot->content_status === 1) {
//                return $q;
//            }
//        })->count();
//        $doubleGreenLightTotal = $videoTotal->filter(function ($q) {
//            if ($q->tbaStatistics->isNotEmpty()) {
//                if ((int)$q->tbaStatistics->first()->T >= 70 && (int)$q->tbaStatistics->first()->P >= 70) {
//                    return $q->tbaStatistics;
//                }
//            }
//        })->count();


//        dd($result);
//        GlobalPlatform::getMemberDuty(3, 948) === null
//            ? $content_public = [1]
//            : $content_public = [1, 0];
//
//        $groupChannelContents = GroupChannelContent::query()
//            ->selectRaw("count(*) as total, group_channel_contents.group_subject_fields_id , alias as subject ,`order`")
//            ->leftJoin('group_subject_fields', 'group_subject_fields.id', 'group_channel_contents.group_subject_fields_id')
////            ->with([
////                'groupSubjectFields' => function ($q) {
////                    $q->selectRaw("id, alias as subject ,`order`")->orderBy('order', 'DESC');
////                }
////            ])
//            ->where('group_id', 3)
//            ->whereIn('content_public', $content_public)
//            ->where('content_status', 1)
//            ->groupBy('group_subject_fields_id')
//            ->orderBy('order', 'DESC')
//            ->orderByRaw("ISNULL(group_subject_fields_id),group_subject_fields_id ASC");
//
//
//        $data = [];
//        $groupChannelContents->get()->map(function ($groupChannelContent) use (&$data) {
//            if ($groupChannelContent->group_subject_fields_id === null) {
//                $data = ['text' => __('app/subject-field.Other'), 'value' => $groupChannelContent->total, 'id' => 'Other'];
//            }
//            $data = ['text' => $groupChannelContent->subject, 'value' => $groupChannelContent->total, 'id' => $groupChannelContent->group_subject_fields_id];
//
//        })->toArray();
//        dd($data);

//        dd($higherOrderBuilderProxy->toArray());
//        $result = [
//            'video_total'              => $videototal->count(),
//            'public_video'             => $publicvideo,
//            'double_green_light_total' => $doublegreenlighttotal,
//            'his_total'                => $videototal->sum('hits'),
//        ];

        $this->assertTrue(true);

//        dd($videoTotal->first());
//        dd($result);
//        $morphToMany = Tba::query()->find(8263)->groupChannels()->attach(500, ['group_id' => 538]);
//        GroupChannelContent::query()->where('content_type', 'Tba')->update(['content_type' => 'App\Models\tba']);
//        dd($higherOrderBuilderProxy);


    }

    public function testGroupForTba()
    {
//        $collection = GroupChannel::query()->where(['group_id' => 7])->first()->tbas()
//            ->orderby('group_subject_fields_id', 'DESC')
//            ->orderby('ratings_id', 'DESC')->get();
        $select = ['ratings.name as rating_name', 'group_subject_fields.alias as alias', 'users.name as users_name', 'users.habook', 'tbas.name as tba_name', 'group_channel_contents.division_id', 'tbas.id'];

        $tbas = Tba::query()
            ->select($select)
            ->join('group_channel_contents', 'tbas.id', 'group_channel_contents.content_id')
            ->join('users', 'tbas.user_id', 'users.id')
            ->join('group_subject_fields', 'group_channel_contents.group_subject_fields_id', 'group_subject_fields.id')
            ->join('ratings', 'group_channel_contents.ratings_id', 'ratings.id')
            ->where('group_channel_contents.group_id', 7)
            ->orderBy('group_channel_contents.group_subject_fields_id', 'DESC')
            ->orderBy('group_channel_contents.ratings_id', 'DESC')
            ->get();

        dd($tbas->toArray());
    }

    public function testGroup()
    {
//        $userInfo = auth()->loginUsingId(948);
        $groupChannel = GroupChannel::query()->find(7)->whereHasMorph(
            'tbas',
            [Tba::class],
            function ($q) {
                $q->where('division_id', 2);
            })->get();
        dd($groupChannel);
//        $groups = $user->groups();
        $group_id = $userInfo->group_channel_id ? GlobalPlatform::convertChannelIdToGroupId($userInfo->group_channel_id) : null;
        $group    = $userInfo->groups->firstWhere('id', $group_id);

    }
}













