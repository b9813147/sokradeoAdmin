<?php

namespace Tests\Feature;

use App\Libraries\Lang\Lang;
use App\Models\DistrictGroupSubject;
use App\Models\DistrictSubject;
use App\Models\Grade;
use App\Models\GroupChannelContent;
use App\Models\GroupSubjectField;
use App\Models\Rating;
use App\Repositories\GroupSubjectFieldsRepository;
use App\Repositories\RatingRepository;
use App\Repositories\SubjectFieldsRepository;
use App\Services\GroupSubjectFieldsService;
use App\Services\RatingService;
use App\Services\SubjectFieldsService;
use Tests\TestCase;

class Subject extends TestCase
{


    public function subjectFieldsService()
    {

        return app(SubjectFieldsRepository::class);
    }

    public function groupSubjectFieldsService()
    {

        return app(GroupSubjectFieldsRepository::class);
    }

    public function testBasic()
    {
//        $group_id          = '141';
//        $data             = [];
//        $data['subjects'] = $this->groupSubjectFieldsService()->getBy('groups_id', 141);
//        $data['area']     = $this->subjectFieldsService()->get();
//
//
//        dd($this->subjectFieldsService()->getAreaLang()->toArray());

//        return $data;
//        dd($data);
//        $this->subjectFieldsService()->get();

    }

    public function testGrade()
    {
        $builders = Grade::query()->with([
            'gradeLang' => function ($q) {
                $q->select('grades_id', 'name');
                $q->where('locales_id', 40);
            }
        ])->get()->toArray();

        dd($builders);

    }

    public function testRating()
    {
        $application      = app(RatingService::class);
        $ratingRepository = app(RatingRepository::class);

        $result = Rating::query()->where(['groups_id' => 7, ['type', '>', 1]])->orderBy('type', 'DESC')->get();
//        dd($result->toArray());
//        $result[0]->type = 3;
        $result[0]->update(['type' => $result[1]->type]);
        $result[1]->update(['type' => $result[0]->type]);
        dd($result->toArray());

//        $byStatistics     = $ratingRepository->changeSort(['groups_id' => 7, ['type', '>', 1]], 1, 2);
//        $byForSort        = $application->getByForSort('groups_id', 7);
        dd($byStatistics->toArray());
    }

    public function testSubjectAddOrder()
    {
        $groupSubjectFields = GroupSubjectField::query()->selectRaw("groups_id, count('order') total")->groupBy('groups_id')->get();
        $groupSubjectFields->each(function ($q) {
            GroupSubjectField::query()->where('groups_id', $q->groups_id)->get()->each(function ($b, $key) {
                $b->order = $key + 1;
                $b->save();
            });
        });
    }

    // todo 學區學科轉換
    public function testDistrictSubjectList()
    {
        $DistrictSubjectIds = DistrictSubject::query()->with([
            'districtGroupSubject' => function ($q) {
                $q->with([
                    'groupSubjectField' => function ($q) {
                        $q->with('districtChannelContent');
                    }
                ]);
            }
        ])
            ->where('districts_id', 1)->get();
        dd($DistrictSubjectIds->toArray());
/*        $DistrictGroupSubject = DistrictGroupSubject::query()->whereIn('district_subjects_id', $DistrictSubjectIds)->with([
            'districtSubject'    => function ($q) {
                $q->select('id', 'subject','order')->orderBy('order','DESC');
            },
            'groupSubjectField' => function ($q) {
                $q->select('id', 'alias');
            }
        ])->get();;*/
//        dd($DistrictGroupSubject->toArray());
//        dd($builders->toArray());
    }

    public function testTotal()
    {
        $public_results   = GroupChannelContent::query()
            ->selectRaw("groups.id,groups.name,groups.school_code,count(tbas.id) as total")
            ->join('tbas', 'tbas.id', 'group_channel_contents.content_id')
            ->join('groups', 'groups.id', 'group_channel_contents.group_id')
            ->whereIn('group_channel_contents.content_status', [1])
            ->where('group_channel_contents.content_public', 1)
            ->where('groups.public', 0)
            ->groupBy('group_channel_contents.group_id')->get();
        $unlisted_results = GroupChannelContent::query()->selectRaw("groups.id, count(tbas.id) as total")
            ->join('tbas', 'tbas.id', 'group_channel_contents.content_id')
            ->join('groups', 'groups.id', 'group_channel_contents.group_id')
            ->whereIn('group_channel_contents.content_status', [1, 2])
            ->where('group_channel_contents.content_public', 0)
            ->where('groups.public', 0)
            ->groupBy('groups.id')->get();


        $result = $public_results->each(function ($public_result) use (&$unlisted_results, &$result) {
            return $unlisted_results->filter(function ($unlisted_result) use (&$public_result, &$result) {
                if ($public_result->id === $unlisted_result->id) {
                    return [
                        $public_result->unlisted_video = $unlisted_result->total
                    ];
                }
            });
        });

        dd($result->toArray());
    }


}
