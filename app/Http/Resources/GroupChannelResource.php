<?php

namespace App\Http\Resources;

use App\Helpers\Custom\GlobalArr;
use App\Helpers\Custom\GlobalPlatform;
use App\Libraries\Lang\Lang;
use App\Models\GradeLang;
use App\Models\GroupSubjectField;
use App\Models\Rating;
use App\Models\TbaComment;
use App\Types\Tba\AnnexType;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin \App\Models\GroupChannel */
class GroupChannelResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $arrayInit          = [];
        $arrayInit['stats'] = [
            'today_total'              => 0,
            'seven_day_total'          => 0,
            'thirty_day_total'         => 0,
            'current_semester'         => 0,
            'public_video'             => 0,
            'video_total'              => 0,
            'double_green_light_total' => 0,
            'material'                 => 0,
            'lessonPlan'               => 0,
            'his_total'                => 0,
            'total_mark'               => 0,
        ];

        $publicVideo      = 1;
        $videoTotal       = 1;
        $hits             = 0;
        $lessonPlan       = 0;
        $material         = 0;
        $cumulative_total = 0;
        $monthInterval    = $this->semesters->pluck('month')->toArray();
        // 判斷是否同月份
        $defaultSameMonth = GlobalPlatform::getNearest($monthInterval, $this->created_at->month) === $this->semesters->pluck('month')->first();
        $defaultYear      = $defaultSameMonth ? $this->created_at->year - 1 : $this->created_at->year;
        $carbon           = Carbon::now();
        $today            = $carbon->format('Y-m-d');
        $month            = GlobalPlatform::getNearest($monthInterval, $carbon->month);
        // 判斷是否同月份
        $currentSameMonth = GlobalPlatform::getNearest($monthInterval, $carbon->month) === $this->semesters->pluck('month')->last();
        // 計算學期
        $semesterCount = $this->semesters->count();

        // 學期數據整理
        $semesterData = $this->semesterData($defaultYear, $semesterCount);
        $defaultSameMonth ? $semesterData->shift() : $semesterData;
        $currentSameMonth ? $semesterData : $semesterData->pop();
        $semesterDataDoubleGreen = $this->semesterData($defaultYear, $semesterCount, true);
        $currentSameMonth ? $semesterDataDoubleGreen->shift() : $semesterDataDoubleGreen;
        $currentSameMonth ? $semesterDataDoubleGreen : $semesterDataDoubleGreen->pop();


        $currentSemester                                = $carbon->year . '-' . 0 . $month . '-' . 0 . 1;
        $arrayInit['stats']['today_total']              = $this->tbas->where('lecture_date', $today)->count();
        $arrayInit['stats']['seven_day_total']          = $this->tbas->whereBetween('lecture_date', [$carbon->sub(7, 'day')->format('Y-m-d'), $today])->count();
        $arrayInit['stats']['thirty_day_total']         = $this->tbas->whereBetween('lecture_date', [$carbon->sub(30, 'day'), $today])->count();
        $arrayInit['stats']['current_semester']         = $this->tbas->whereBetween('lecture_date', [$currentSemester, $today])->count();
        $arrayInit['stats']['double_green_light_total'] = $this->tbas->where('double_green_light_status', 1)->count(); // 雙綠燈總數
        $arrayInit['semester']                          = []; // 學期預設值
        $arrayInit['group_subject_fields']              = $this->groupSubjectFields->toArray();
        $arrayInit['semester']['current_semester']      = $currentSemester;
        $arrayInit['semester']['start_semester']        = $this->created_at->format('Y-m-d');
        $arrayInit['semester']['total']                 = $semesterData->all();
        $arrayInit['semester']['double_green_light']    = $semesterDataDoubleGreen->all();

        // 使用空間
        $blobVideo       = 0;
        $oldVideo        = 0;
        $hiTeachNoteFile = 0;
        $lessonPlanFile  = 0;
        $materialFile    = 0;
        $total           = 0;

//        $hiTeachIds    = '';
//        $lessonPlanIds = '';
//        $materialIds   = '';
//        $url           = storage_path("app/file/");
        $this->tbas->map(function ($q) use (&$hiTeachNoteFile, &$lessonPlanFile, &$materialFile, &$arrayInit, &$material, &$lessonPlan, &$hits, &$videoTotal, &$publicVideo, &$blobVideo, &$oldVideo) {
            if ($q->tbaAnnexs->isNotEmpty()) {
                $q->tbaAnnexs->each(function ($q) use (&$materialFile, &$lessonPlanFile, &$hiTeachNoteFile, &$arrayInit, &$material, &$lessonPlan) {
                    switch ($q->type) {
                        case AnnexType::HiTeachNote:
                            $hiTeachNoteFile += $q->size;
                            break;
                        case AnnexType::LessonPlan:
                            $lessonPlanFile += $q->size;
                            break;
                        default:
                            $materialFile += $q->size;
                    }
                    // 課件數
                    if ($q->type === AnnexType::LessonPlan) {
                        $arrayInit['stats']['lessonPlan'] = $lessonPlan++;
                    }
                    // 教案數
                    if ($q->type === AnnexType::Material) {
                        $arrayInit['stats']['material'] = $material++;
                    }
                });
            }
            // 公開影片數
            if ($q->pivot->content_public === 1 && $q->pivot->content_status === 1) {
                $arrayInit['stats']['public_video'] = $publicVideo++;
            }
            // 影片總數 & 點閱總數
            if (($q->pivot->content_status === 1 || $q->pivot->content_status === 2) || ($q->pivot->content_pulic === 1 || $q->pivot->content_pulic === 0)) {
                $arrayInit['stats']['video_total'] = $videoTotal++;
                $arrayInit['stats']['his_total']   = $hits += $q->hits;
            }

            if ($q->videos->isNotEmpty() && $q->pivot->share_status) {
                if ($q->videos->first()->encoder === 'FileUpload') {
                    $oldVideo += $q->videos->first()->resource->vod->file_size ?? 0;
                }
                $blobVideo += $q->videos->first()->resource->vod->file_size ?? 0;
            }
        });

//        dd($hiTeachIds);
//        $hiTeachNoteFile = (int)trim(shell_exec("du -cb $hiTeachIds | grep total |  awk '{print $1}'"));
//        $lessonPlanFile  = (int)trim(shell_exec("du -cb $lessonPlanIds | grep total |  awk '{print $1}'"));
//        $materialFile    = (int)trim(shell_exec("du -cb $materialIds | grep total |  awk '{print $1}'"));

        try {
            $total = $this->group->bbLicenses->sum('pivot.storage') * 1024 * 1024 ?? 0;
        } catch (\Exception $exception) {
            $total = 0;
        }

        $arrayInit['storage']['blob']        = $blobVideo;
        $arrayInit['storage']['old']         = $oldVideo;
        $arrayInit['storage']['hiTeachNote'] = $hiTeachNoteFile;
        $arrayInit['storage']['lessonPlan']  = $lessonPlanFile;
        $arrayInit['storage']['material']    = $materialFile;
        $arrayInit['storage']['total']       = $total;


        $ratings      = $this->tbas->groupBy('pivot.ratings_id');
        $grades       = $this->tbas->groupBy('pivot.grades_id');
        $subjects     = $this->tbas->groupBy('pivot.group_subject_fields_id');
        $ratingsModel = Rating::query()->select('id', 'name')->where('groups_id', $this->group_id)->orderBy('id')->get();
//        // 教研分類
        $ratings->each(function ($v, $k) use (&$arrayInit, $ratingsModel) {
            $arrayInit['ratings'][] = [
                'name'  => $ratingsModel->firstWhere('id', $k)->name ?? 'other',
                'id'    => $k,
                'total' => count($v),
            ];
        });
        // 年級統計
        $locales_id = Lang::getConvertByLangStringForId();
        $gradeNames = GradeLang::query()->select('name', 'grades_id', 'id')->where('locales_id', $locales_id)->orderBy('id')->get();
        $gradeList  = [];
        foreach ($gradeNames as $gradeName) {
            $array[$gradeName->grades_id] = $gradeName->name;
        }

        $grades->each(function ($v, $k) use (&$gradeList, &$arrayInit, $locales_id) {
            $arrayInit['grades'][] = [
                'name'  => !empty($gradeList[$k])
                    ? $gradeList[$k]
                    : 'other',
                'id'    => $k ?: 13,
                'total' => count($v),
            ];
        });
        if (!empty($arrayInit['grades'])) {
            $arrayInit['grades'] = array_values(GlobalArr::sort($arrayInit['grades'], function ($value) {
                return $value['id'];
            }));
        }

        $groupSubjectFieldModel = GroupSubjectField::query()->where('groups_id', $this->group_id)->select('groups_id', 'id', 'alias', 'order')->orderBy('id')->get();
        // 學科統計
        $subjects->each(function ($v, $k) use (&$arrayInit, $locales_id, $groupSubjectFieldModel) {
            $arrayInit['subjects'][] = [
                'name'  => !empty($groupSubjectFieldModel->firstWhere('id', $k)->alias) ? $groupSubjectFieldModel->firstWhere('id', $k)->alias : 'other',
                'id'    => $k ?: 0,
                'order' => !empty($groupSubjectFieldModel->firstWhere('id', $k)->order) ? $groupSubjectFieldModel->firstWhere('id', $k)->order : 0,
                'total' => count($v),
            ];
        });
        if (!empty($arrayInit['subjects'])) {
            $arrayInit['subjects'] = array_values(GlobalArr::rsort($arrayInit['subjects'], function ($value) {
                return $value['order'];
            }));
        }

        // 影片公開 標記數點評的數量
        $arrayInit['stats']['total_mark'] = TbaComment::query()->select('tba_id')->whereIn('tba_id', $this->tbas->pluck('id'))->whereNull('nick_name')->orderBy('id')->count();

        return $arrayInit;
    }

    /**
     * @param int $startYear
     * @param int $semesterCount
     * @param bool $double_green_light
     * @return \Illuminate\Support\Collection
     */
    private function semesterData(int $startYear, int $semesterCount, bool $double_green_light = false): \Illuminate\Support\Collection
    {
        $carbon           = Carbon::now();
        $defaultYear      = $startYear; // 預設學期
        $firstSemester    = null;
        $seconderSemester = null;
        $thirdSemester    = null;
        $fourthSemester   = null;
        $result           = collect();

        // 取出全部 學期
        $this->semesters()->each(function ($q) use (&$firstSemester, &$seconderSemester, &$thirdSemester, &$fourthSemester) {
            $resultSemester = (Str::length($q->month) < 2 ? '0' . $q->month : $q->month) . '-' . (Str::length($q->day) < 2 ? '0' . $q->day : $q->day);
            switch ($q->semester_id) {
                case 2:
                    $seconderSemester = $resultSemester;
                    break;
                case 3:
                    $thirdSemester = $resultSemester;
                    break;
                case 4:
                    $fourthSemester = $resultSemester;
                    break;
                default:
                    $firstSemester = $resultSemester;
                    break;
            }
        });
        // 當前學年
        $currentYear = $carbon->format('m-d') > $firstSemester ? $carbon->year : $carbon->year - 1;

        while ($defaultYear <= $currentYear) {
            switch ($semesterCount) {
                case 3:
                    $firstData    = $this->tbas()->whereBetween('lecture_date', [
                        $defaultYear . '-' . $firstSemester,
                        ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $seconderSemester : $defaultYear + 1 . '-' . $seconderSemester
                    ])
                        ->when($double_green_light, function ($q) {
                            $q->where('double_green_light_status', 1);
                        })->count();
                    $seconderData = $this->tbas()->whereBetween('lecture_date', [
                        ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $seconderSemester : $defaultYear + 1 . '-' . $seconderSemester,
                        ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $thirdSemester : $defaultYear + 1 . '-' . $thirdSemester
                    ])
                        ->when($double_green_light, function ($q) {
                            $q->where('double_green_light_status', 1);
                        })->count();
                    $thirdData    = $this->tbas()->where('lecture_date', [
                        ($thirdSemester > $firstSemester) ? $defaultYear . '-' . $thirdSemester : $defaultYear + 1 . '-' . $thirdSemester,
                        ($thirdSemester > $firstSemester) ? explode('-', $firstSemester)[0] === '01'
                            ? $defaultYear + 1 . '-' . $firstSemester
                            : $defaultYear . '-' . $firstSemester
                            : $defaultYear + 1 . '-' . $firstSemester
                    ])
                        ->when($double_green_light, function ($q) {
                            $q->where('double_green_light_status', 1);
                        })->count();

                    $result->push([
                        'year'  => $defaultYear . '(1)',
                        'total' => $firstData
                    ]);
                    $result->push([
                            'year'  => $defaultYear . '(2)',
                            'total' => $seconderData
                        ]
                    );
                    $result->push([
                            'year'  => $defaultYear . '(3)',
                            'total' => $thirdData
                        ]
                    );
                    break;
                case 4:
                    $firstData    = $this->tbas()->whereBetween('lecture_date', [
                        $defaultYear . '-' . $firstSemester,
                        ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $seconderSemester : $defaultYear + 1 . '-' . $seconderSemester
                    ])
                        ->when($double_green_light, function ($q) {
                            $q->where('double_green_light_status', 1);
                        })->count();
                    $seconderData = $this->tbas()->whereBetween('lecture_date', [
                        ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $seconderSemester : $defaultYear + 1 . '-' . $seconderSemester,
                        ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $thirdSemester : $defaultYear + 1 . '-' . $thirdSemester
                    ])
                        ->when($double_green_light, function ($q) {
                            $q->where('double_green_light_status', 1);
                        })->count();
                    $thirdData    = $this->tbas()->where('lecture_date', [
                        ($thirdSemester > $firstSemester) ? $defaultYear . '-' . $thirdSemester : $defaultYear + 1 . '-' . $thirdSemester,
                        ($thirdSemester > $firstSemester) ? $defaultYear . '-' . $fourthSemester : $defaultYear + 1 . '-' . $fourthSemester
                    ])
                        ->when($double_green_light, function ($q) {
                            $q->where('double_green_light_status', 1);
                        })->count();
                    $fourthData   = $this->tbas()->whereBetween('lecture_date', [
                        ($thirdSemester > $firstSemester) ? $defaultYear . '-' . $fourthSemester : $defaultYear + 1 . '-' . $fourthSemester,
                        ($fourthSemester > $firstSemester) ? explode('-', $firstSemester)[0] === '01'
                            ? $defaultYear + 1 . '-' . $firstSemester
                            : $defaultYear . '-' . $firstSemester
                            : $defaultYear + 1 . '-' . $firstSemester
                    ])
                        ->when($double_green_light, function ($q) {
                            $q->where('double_green_light_status', 1);
                        })->count();
                    $result->push([
                        'year'  => $defaultYear . '(1)',
                        'total' => $firstData
                    ]);
                    $result->push([
                            'year'  => $defaultYear . '(2)',
                            'total' => $seconderData
                        ]
                    );
                    $result->push([
                            'year'  => $defaultYear . '(3)',
                            'total' => $thirdData
                        ]
                    );
                    $result->push([
                            'year'  => $defaultYear . '(4)',
                            'total' => $fourthData,
                        ]
                    );
                    break;
                default:
                    $firstData    = $this->tbas()
                        ->whereBetween('lecture_date', [$defaultYear . '-' . $firstSemester, ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $seconderSemester : $defaultYear + 1 . '-' . $seconderSemester])
                        ->when($double_green_light, function ($q) {
                            $q->where('double_green_light_status', 1);
                        })->count();
                    $seconderData = $this->tbas()
                        ->whereBetween('lecture_date', [$defaultYear + 1 . '-' . $seconderSemester, $defaultYear + 1 . '-' . $firstSemester])
                        ->when($double_green_light, function ($q) {
                            $q->where('double_green_light_status', 1);
                        })->count();

                    $result->push([
                        'year'  => $defaultYear . '(1)',
                        'total' => $firstData
                    ]);
                    $result->push([
                            'year'  => $defaultYear . '(2)',
                            'total' => $seconderData
                        ]
                    );
            }

            $defaultYear++;
        }
        return $result;
    }
}
