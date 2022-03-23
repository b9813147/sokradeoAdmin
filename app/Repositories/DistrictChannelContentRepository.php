<?php

namespace App\Repositories;

use App\Models\DistrictChannelContent;
use Illuminate\Database\Query\Builder;

class DistrictChannelContentRepository extends BaseRepository
{
    /**
     * @var DistrictChannelContent
     *  @var $model Builder
     */
    protected $model;

    /**
     * DistrictChannelContentRepository constructor.
     * @param $model
     */
    public function __construct(DistrictChannelContent $model)
    {
        $this->model = $model;
    }

    /**
     * 學區課例分類
     * @param int $districtId
     * @param int $localesId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function districtChannelContentInfo(int $districtId, int $localesId)
    {
        return $this->model->query()->with([
                'district'             => function ($q) {
                    $q->select('abbr', 'id');
                },
                'grade'                => function ($q) use ($localesId) {
                    $q->with([
                        'gradeLang' => function ($q) use ($localesId) {
                            $q->select('name', 'grades_id')->where('locales_id', $localesId);
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
        )->where('districts_id', $districtId)->get();
    }

    public function districtChannelSubjectInfo($districtId)
    {
        return $this->model->query()
            ->with([
                    'groupSubjectField'    => function ($q) {
                        $q->select('alias', 'id');
                    },
                    'group'                => function ($q) {
                        $q->select('name', 'id');
                    },
                    'districtGroupSubject' => function ($q) {
                        $q->with([
                            'districtSubject' => function ($q) {
                                $q->select('id', 'subject');
                            }
                        ])->select('district_subjects_id', 'group_subject_fields_id');
                    },

                ]
            )->groupBy('group_subject_fields_id')
            ->whereNotNull('group_subject_fields_id')
            ->where('districts_id', $districtId)
            ->get();
    }
}
