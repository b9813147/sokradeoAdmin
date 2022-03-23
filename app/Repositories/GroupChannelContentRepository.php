<?php

namespace App\Repositories;

use App\Helpers\Custom\GlobalPlatform;
use App\Libraries\Lang\Lang;
use App\Models\DistrictChannelContent;
use App\Models\DistrictGroupSubject;
use App\Models\DistrictSubject;
use App\Models\GroupChannelContent;
use App\Models\GroupSubjectField;
use App\Models\Rating;
use App\Models\RecommendedVideo;
use App\Models\Tba;
use App\Types\Cms\CmsType;
use Illuminate\Database\Eloquent\Model;


class GroupChannelContentRepository extends BaseRepository
{
    /**
     * @var $model  \Illuminate\Database\Eloquent\Builder|Model|GroupChannelContent
     */
    protected $model;

    /**
     * GroupChannelContentRepository constructor.
     * @param GroupChannelContent $groupChannelContent
     */
    public function __construct(GroupChannelContent $groupChannelContent)
    {
        $this->model = $groupChannelContent;
    }

    /**
     * @param integer $group_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function getGroupChannelContentList(int $group_id)
    {
        // 影片篩選
        $content = $this->model->where(['group_id' => $group_id, 'content_type' => CmsType::Tba]);
        // 取出影片ID
        $tbaId = $content->pluck('content_id');
        $tbas  = Tba::query()
            ->with([
                'tbaStatistics'       => function ($q) {
                    $q->whereBetween('type', [55, 60])->select('tba_id', 'type', 'idx');
                },
                'groupChannelContent' => function ($q) use ($group_id) {
                    $q->where(['group_id' => $group_id, 'content_type' => CmsType::Tba])
                        ->with([
                            'groupSubjectField' => function ($q) use ($group_id) {
                                $q->select('groups_id', 'subject', 'id', 'alias')->where('groups_id', $group_id);
                            }, 'rating'         => function ($q) use ($group_id) {
                                $q->select('groups_id', 'name', 'id', 'type')->where('groups_id', $group_id);
                            }, 'gradesLang'     => function ($q) {
                                $q->select('locales_id', 'name', 'id', 'grades_id')->where('locales_id', Lang::getConvertByLangStringForId());
                            }
                        ])
                        ->select('content_id', 'content_status', 'content_public', 'group_subject_fields_id', 'ratings_id', 'grades_id', 'share_status')
                        ->orderBy('content_id', 'DESC');
                }
            ])
            ->select('id', 'name', 'thumbnail', 'description', 'teacher', 'habook_id', 'course_core', 'observation_focus')
            ->selectRaw("created_at date")
            ->whereIn('id', $tbaId)
            ->orderBy('created_at', 'DESC')
            ->get();

        return $tbas;
    }

    public function getGroupChannelContentAndTbaByGroupChannelId($groupChannelId)
    {
        $group_channel_content     = GroupChannelContent::query()->select('content_id')->where(['group_channel_id' => $groupChannelId, 'content_status' => 1, 'content_public' => 1]);
        $group_channel_content_ids = $group_channel_content->pluck('content_id');
        $recommended_video         = RecommendedVideo::query()->select('tba_id', 'group_channel_id');
        $recommended_video_tba_ids = $recommended_video->pluck('tba_id');
        $tbas                      = Tba::query()->whereIn('id', $group_channel_content_ids)->whereNotIn('id', $recommended_video_tba_ids)->select('id', 'name')->get();
        return $tbas;

    }

    /**
     * 更新groupChannelContent 與 Tba
     *
     * @param object $request
     * @param integer $group_id
     * @param null $thumbnail
     * @throws \Exception
     */
    public function updateGroupChannelContentAndTba(object $request, int $group_id, $thumbnail = null)
    {

        $status      = GlobalPlatform::convertChannelStatusToSql($request->status);
        $data        = [
            'group_subject_fields_id' => (int)$request->subject ?: null,
            'ratings_id'              => (int)$request->rating,
            'author_id'               => $request->user_id
        ];
        $array_merge = array_merge($status, $data);

        // Update GroupChannelContent Status
        $this->model->where([
            'group_id'   => $group_id,
            'content_id' => $request->id,
        ])->update($array_merge);

        // Update GroupChannelContent Grades
        $this->model->where('content_id', $request->id)->update([
            'grades_id' => ($request->grade == 'Other') ? null : $request->grade
        ]);

        // Update DistrictChannelContent Synchronize
        $districtId = GlobalPlatform::convertGroupIdToDistrictId($group_id);
        if (!empty($districtId) && (int)$request->status === 3) {
            GroupSubjectField::query()->where('groups_id', $group_id)->get()->each(function ($groupSubjectField) use ($districtId) {
                DistrictGroupSubject::query()->updateOrInsert([
                    'group_subject_fields_id' => $groupSubjectField->id,
                ], [
                    'group_subject_fields_id' => $groupSubjectField->id,
                    'district_subjects_id'    => DistrictSubject::query()->where('districts_id', $districtId)
                            ->where('subject', $groupSubjectField->subject)->pluck('id')->first() ?? null
                ]);
            });

            DistrictChannelContent::query()->updateOrInsert([
                'content_id'   => $request->id,
                'districts_id' => GlobalPlatform::convertGroupIdToDistrictId($group_id),
                'groups_id'    => $group_id,
            ],
                [
                    'grades_id'               => ($request->grade == 'Other') ? null : $request->grade,
                    'group_subject_fields_id' => (int)$request->subject ?: null,
                    'ratings_id'              => Rating::query()->where(['districts_id' => $districtId, 'type' => 0])->pluck('id')->first() ?: null,
                    'groups_id'               => $group_id,
                    'content_id'              => $request->id,
                    'districts_id'            => $districtId
                ]);
        } elseif (!empty($districtId) && $request->status != 3) {
            DistrictChannelContent::query()->where([
                'content_id'   => $request->id,
                'districts_id' => $districtId,
                'groups_id'    => $group_id,
            ])->delete();
        }


        // update Tba
        $tba = Tba::query()->findOrfail($request->id);
        if ($thumbnail) {
            $tba->update([
                'name'              => $request->name,
                'thumbnail'         => $thumbnail,
                'teacher'           => $request->teacher,
                'habook_id'         => $request->habook,
                'course_core'       => $request->course_core,
                'observation_focus' => $request->observation_focus,
//                'educational_stage_id' => $request->educational_stage_id,
                'description'       => $request->description
            ]);
            return;
        }
        $tba->update([
            'name'              => $request->name,
            'teacher'           => $request->teacher,
            'habook_id'         => $request->habook,
            'course_core'       => $request->course_core,
            'observation_focus' => $request->observation_focus,
//            'educational_stage_id' => $request->educational_stage_id,
            'description'       => $request->description
        ]);
    }


    /**
     * 影片數 排除重複
     * @param array $content_status
     * @param int $content_public
     * @return int
     */
    public function videoTotal(array $content_status, int $content_public)
    {
        return $this->model->select('tbas.id')
            ->join('tbas', 'tbas.id', 'group_channel_contents.content_id')
            ->whereIn('content_status', $content_status)
            ->where('content_public', $content_public)
            ->distinct()
            ->count('tbas.id');
    }

    /**
     * 學校影片數
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function videoTotalByGroup()
    {
        $public_results   = $this->model->selectRaw("groups.id,groups.name,groups.school_code,count(tbas.id) as total")
            ->join('tbas', 'tbas.id', 'group_channel_contents.content_id')
            ->join('groups', 'groups.id', 'group_channel_contents.group_id')
            ->where('group_channel_contents.content_status', 1)
            ->where('group_channel_contents.content_public', 1)
            ->where('groups.public', 0)
            ->groupBy('group_channel_contents.group_id')->get();
        $unlisted_results = $this->model->selectRaw("groups.id, count(tbas.id) as total")
            ->join('tbas', 'tbas.id', 'group_channel_contents.content_id')
            ->join('groups', 'groups.id', 'group_channel_contents.group_id')
            ->whereIn('group_channel_contents.content_status', [1, 2])
            ->where('group_channel_contents.content_public', 0)
            ->where('groups.public', 0)
            ->groupBy('groups.id')->get();


        return $public_results->each(function ($public_result) use (&$unlisted_results, &$result) {
            return $unlisted_results->filter(function ($unlisted_result) use (&$public_result, &$result) {
                if ($public_result->id === $unlisted_result->id) {
                    return [
                        $public_result->unlisted_video = $unlisted_result->total
                    ];

                }

            });
        });
    }

    public function getSubjectCount()
    {
//        $this->model->with('groupSubjectField')->where()
    }
}
