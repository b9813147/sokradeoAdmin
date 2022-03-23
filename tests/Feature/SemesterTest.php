<?php

namespace Tests\Feature;

use App\Models\GroupChannel;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class SemesterTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testAllForSemester()
    {
        $double_green_light = true;
        $carbon             = Carbon::now();
        $group_id           = 518; // 頻道ID
        $defaultYear        = 2017; // 預設學期
        $semester           = Semester::query()->where('group_id', $group_id)->get(); // 學期資料
        $firstSemester      = null;
        $seconderSemester   = null;
        $thirdSemester      = null;
        $fourthSemester     = null;
        // 取出全部 學期
        $semester->each(function ($q) use (&$firstSemester, &$seconderSemester, &$thirdSemester, &$fourthSemester) {
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
        $currentYear  = $carbon->format('m-d') > $firstSemester ? $carbon->year : $carbon->year - 1;
        $semesterList = collect();
        while ($defaultYear <= $currentYear) {
            $tbas = GroupChannel::with('tbas')->where('group_id', $group_id)->first()->tbas;
            switch ($semester->count()) {
                case 3:
                    $firstData = $tbas
                        ->where('lecture_date', '>', $defaultYear . '-' . $firstSemester)
                        ->where('lecture_date', '<', ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $seconderSemester : $defaultYear + 1 . '-' . $seconderSemester)
                        ->count();

                    $seconderData = $tbas
                        ->where('lecture_date', '>=', ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $seconderSemester : $defaultYear + 1 . '-' . $seconderSemester)
                        ->where('lecture_date', '<', ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $thirdSemester : $defaultYear + 1 . '-' . $thirdSemester)
                        ->count();

                    $thirdData = $tbas
                        ->where('lecture_date', '>=', ($thirdSemester > $firstSemester) ? $defaultYear . '-' . $thirdSemester : $defaultYear + 1 . '-' . $thirdSemester)
                        ->where('lecture_date', '<', ($thirdSemester > $firstSemester)
                            ? explode('-', $firstSemester)[0] === '01'
                                ? $defaultYear + 1 . '-' . $firstSemester
                                : $defaultYear . '-' . $firstSemester
                            : $defaultYear + 1 . '-' . $firstSemester)
                        ->count();

                    $semesterList->push([
                        'year'  => $defaultYear . '-' . '01',
                        'total' => $firstData
                    ]);
                    $semesterList->push([
                            'year'  => $defaultYear . '-' . '02',
                            'total' => $seconderData
                        ]
                    );
                    $semesterList->push([
                            'year'  => $defaultYear . '-' . '03',
                            'total' => $thirdData
                        ]
                    );
                    break;
                case 4:
                    $firstData = $tbas
                        ->where('lecture_date', '>', $defaultYear . '-' . $firstSemester)
                        ->where('lecture_date', '<', ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $seconderSemester : $defaultYear + 1 . '-' . $seconderSemester)
                        ->count();

                    $seconderData = $tbas
                        ->where('lecture_date', '>=', ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $seconderSemester : $defaultYear + 1 . '-' . $seconderSemester)
                        ->where('lecture_date', '<', ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $thirdSemester : $defaultYear + 1 . '-' . $thirdSemester)
                        ->count();

                    $thirdData = $tbas
                        ->where('lecture_date', '>=', ($thirdSemester > $firstSemester) ? $defaultYear . '-' . $thirdSemester : $defaultYear + 1 . '-' . $thirdSemester)
                        ->where('lecture_date', '<', ($thirdSemester > $firstSemester) ? $defaultYear . '-' . $fourthSemester : $defaultYear + 1 . '-' . $fourthSemester)
                        ->count();

                    $fourthData = $tbas
                        ->where('lecture_date', '>=', ($fourthSemester > $firstSemester) ? $defaultYear . '-' . $fourthSemester : $defaultYear + 1 . '-' . $fourthSemester)
                        ->where('lecture_date', '<', ($fourthSemester > $firstSemester)
                            ? explode('-', $firstSemester)[0] === '01'
                                ? $defaultYear + 1 . '-' . $firstSemester
                                : $defaultYear . '-' . $firstSemester
                            : $defaultYear + 1 . '-' . $firstSemester
                        )
                        ->count();
;
                    $semesterList->push([
                        'year'  => $defaultYear . '-' . '01',
                        'total' => $firstData
                    ]);
                    $semesterList->push([
                            'year'  => $defaultYear . '-' . '02',
                            'total' => $seconderData
                        ]
                    );
                    $semesterList->push([
                            'year'  => $defaultYear . '-' . '03',
                            'total' => $thirdData
                        ]
                    );
                    $semesterList->push([
                            'year'  => $defaultYear . '-' . '04',
                            'total' => $fourthData,
                        ]
                    );
                    break;
                default:
                    $firstData    = $tbas->where('lecture_date', '>', $defaultYear . '-' . $firstSemester)
                        ->where('lecture_date', '<', ($seconderSemester > $firstSemester) ? $defaultYear . '-' . $seconderSemester : $defaultYear + 1 . '-' . $seconderSemester)
                        ->count();
                    $seconderData = $tbas->where('lecture_date', '>=', $defaultYear + 1 . '-' . $seconderSemester)
                        ->where('lecture_date', '<', $defaultYear + 1 . '-' . $firstSemester)->count();

                    $semesterList->push([
                        'year'  => $defaultYear . '-' . '01',
                        'total' => $firstData
                    ]);
                    $semesterList->push([
                            'year'  => $defaultYear . '-' . '02',
                            'total' => $seconderData
                        ]
                    );

            }
            $defaultYear++;
        }
        dd($semesterList);
        return $semesterList;
    }
}
