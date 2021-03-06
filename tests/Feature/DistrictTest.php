<?php

namespace Tests\Feature;

use App\Helpers\Custom\GlobalPlatform;
use App\Libraries\Lang\Lang;
use App\Models\DistrictChannelContent;
use App\Models\DistrictGroupSubject;
use App\Models\Districts;
use App\Models\DistrictSubject;
use App\Models\DistrictUser;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\GroupChannelContent;
use App\Models\GroupSubjectFields;
use App\Models\Rating;
use App\Models\User;
use App\Repositories\DistrictUserRepository;
use App\Services\DistrictService;
use App\Services\Library\BbService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DistrictTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testRepository()
    {
        $districtId = '';
        $localesId  = '';
        $builders   = DistrictChannelContent::query()->with([
                'district'             => function ($q) {
                    $q->select('abbr', 'id');
                },
                'grade'                => function ($q) {
                    $q->with([
                        'gradeLang' => function ($q) {
                            $q->select('name', 'grades_id')->where('locales_id', 65);
                        }
                    ])->select('id');
                },
                'rating'               => function ($q) {
                    $q->select('name', 'id');
                },
                'districtGroupSubject' => function ($q) {
                    $q->with([
                            'districtSubject' => function ($q) {
                                $q->select('id', 'alias');
                            }
                        ]
                    )->select('group_subject_fields_id', 'district_subjects_id');
                },
                'tba'                  => function ($q) {
                    $q->select('name', 'id');
                }
            ]
        )->where('districts_id', 1)->get();

        dd($builders->toArray());
    }

    public function testAddGroup()
    {
        $defaultSubject = collect([
            [
                'subject'           => '??????',
                'alias'             => '??????',
                'groups_id'         => 1,
                'subject_fields_id' => 1
            ],
            [
                'subject'           => '??????',
                'alias'             => '??????',
                'groups_id'         => 1,
                'subject_fields_id' => 1
            ],
            [
                'subject'           => '??????',
                'alias'             => '??????',
                'groups_id'         => 1,
                'subject_fields_id' => 1
            ],

        ]);
        $defaultSubject->each(function ($q) {

        });


        $model = Group::query()->first();
        $model->groupLangs()->create([
            'name'       => '??????????????????????????????test',
            'locales_id' => 30
        ]);
    }

    public function testCreateDistrict()
    {
        $district = [
            'abbr'        => 'test',
            'school_code' => 'test',
            'thumbnail'   => 'test',
            'status'      => '1',
        ];

        $district_lang = (object)[
            'name'        => 'tset',
            'description' => 'tset',
        ];
        $group_ids     = collect([
            4, 7, 8
        ]);
//        Districts::query()->find(6)->districtGroups()->attach([5]);
        $diff_Ids        = $group_ids->diff(Districts::query()->find(6)->groups()->get()->pluck('id'));
        $diff_delete_Ids = Districts::query()->find(6)->groups()->get()->pluck('id')->diff($group_ids);
        dd($diff_delete_Ids);

        dd(Districts::query()->find(6)->groups()->attach($diff_Ids));

        $districtsInfo = Districts::query()->create($district);

        $this->createDefaultValue('tw', $districtsInfo->id, $district_lang, $group_ids);


    }

    /**
     * @param string $lang
     * @param int $district_id
     * @param object $district_lang
     * @param array $group_ids
     */
    private function CreateDefaultValue(string $lang, int $district_id, object $district_lang, array $group_ids)
    {
        $districtService = app(DistrictService::class);
        $districtInfo    = $districtService->find($district_id);

        $districtInfo->ratings()->delete();
        $districtInfo->districtLang()->delete();

        // ??????
        $locales_ids = collect([37, 40, 65]);
        // ??????
        $ratings = collect([
            'cn' => [
                ['type' => '1', 'name' => '??????'],
                ['type' => '2', 'name' => '??????'],
                ['type' => '3', 'name' => '??????'],
                ['type' => '4', 'name' => '??????'],
                ['type' => '5', 'name' => '??????'],
            ],
            'tw' => [
                ['type' => '1', 'name' => '??????'],
                ['type' => '2', 'name' => '??????'],
                ['type' => '3', 'name' => '??????'],
                ['type' => '4', 'name' => '??????'],
                ['type' => '5', 'name' => '??????'],
            ],
            'en' => [
                ['type' => '1', 'name' => 'Study'],
                ['type' => '2', 'name' => 'Good'],
                ['type' => '3', 'name' => 'Great'],
                ['type' => '4', 'name' => 'Perfect'],
                ['type' => '5', 'name' => 'Excellent'],
            ]
        ]);

        // ??????
        $ratings->each(function ($v, $k) use ($lang, $districtInfo) {
            if ($k === $lang) {
                collect($v)->each(function ($v) use ($districtInfo) {
                    $districtInfo->ratings()->create($v);
                });
            }
        });
        // ??????
        $locales_ids->each(function ($v) use ($district_lang, $districtInfo) {
            $districtInfo->districtLang()->create(['name' => $district_lang->name, 'description' => $district_lang->description, 'locales_id' => $v,]);
        });

        // ??????????????????
        collect($group_ids)->each(function ($v) use ($districtInfo) {
            $districtInfo->districtGroups()->create(['groups_id' => $v]);
        });
    }

    /**
     * ???????????? district_channel_content
     * @param int $district_id
     */
    public function testGetDistrictByChannelMovie(int $district_id = 6)
    {
        $district = Districts::query()->find($district_id);
        $groupIds = [];
        // ???????????? $groupIds
        $groupIds = $district->groups->map(function ($q) {
            return $q->id;
        });

        $rating = $district->ratings->first();
        GroupChannelContent::query()->whereIn('group_id', $groupIds)->where(['content_status' => 1, 'content_public' => 1])->get()->each(function ($q) use ($district, $rating) {
            $district->districtChannelContents()->updateOrCreate(
                [
                    'content_id' => $q->content_id,
                    'groups_id'  => $q->group_id,
                ], [
                    'ratings_id'              => $rating->id,
                    'grades_id'               => $q->grades_id,
                    'group_subject_fields_id' => $q->group_subject_fields_id,
                ]
            );
        });
    }

    /**
     * ??????????????????
     */
    public function testDistrictSubject(int $district_id = 6)
    {
        $district = Districts::query()->find($district_id);
        $groupIds = [];
        // ???????????? $groupIds
        $groupIds = $district->groups->map(function ($q) {
            return $q->id;
        });
        Group::query()->whereIn('id', $groupIds)->get()->each(function ($v) use ($district) {
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
    }

    /**
     * ?????? ???????????????????????????
     */
    public function testDistrictGroupSubject(int $district_id = 6)
    {
        $district = Districts::query()->find($district_id);
        $groupIds = [];
        // ???????????? $groupIds
        $groupIds = $district->groups->map(function ($q) {
            return $q->id;
        });

        Group::query()->whereIn('id', $groupIds)->get()->each(function ($v) use ($district) {
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

    public function testGroup()
    {
        
    }
}
