<?php

namespace Tests\Feature;

use App\Libraries\Lang\Lang;
use App\Models\Districts;
use App\Models\GroupChannel;
use App\Models\GroupSubjectField;
use App\Models\Semester;
use App\Services\DistrictUserService;
use App\Types\Group\DutyType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function testBasic()
    {
        $response = Http::withHeaders(['accept' => 'application/vnd.sokradeo.v1+json'])->get('http://adminvuetify.com/api/dashboard/3');
        dump($response->json());
        $this->assertTrue(true);
    }

    public function testSemester()
    {
        $groupChannels = GroupChannel::query()->with([
            'semesters' => function ($q) {
                $q->select('semester_id', 'month', 'day', 'group_id');
            },
            'tbas'      => function ($q) {
//                $q->where('double_green_light_status', 1);
//                $q->with([
//                    'tbaPlaylistTracks' => function ($q) {
//                        $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
//                            ->orderBy('tba_playlist_tracks.list_order');
//                    },
//                    'tbaStatistics'     => function ($q) {
//                        $q->selectRaw('MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T
//                            ,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P
//                            ,tba_statistics.tba_id
//                            ,MAX(CASE WHEN type = 55 THEN CAST(idx AS signed) ELSE 0 END) AS C')
//                            ->groupBy('tba_statistics.tba_id');
//                    },
//                    'tbaAnnexs'
//                ]);
            }
        ])->findOrFail(3);

        $groupChannels->semesters()->each(function ($q) use (&$firstSemester, &$seconderSemester, &$thirdSemester, &$fourthSemester) {
            $resultSemester = (Str::length($q->month) < 2 ? '0' . $q->month : $q->month) . '-' . (Str::length($q->day) < 2 ? '0' . $q->day : $q->day);
            dump($resultSemester);
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
        $carbon      = new \Illuminate\Support\Carbon();
        $currentYear = $carbon->format('m-d') > $firstSemester ? $carbon->year : $carbon->year - 1;

        $semester       = []; // 學期數據
        $firstSemesters = $groupChannels->tbas()->whereBetween(\DB::raw("month(lecture_date)"), [8, 12])
            ->groupByRaw('year(lecture_date)')->selectRaw("count(tbas.id) total,year(lecture_date) year")->get()
            ->map(function ($q) {
                return [
                    'year'  => $q->year,
                    'total' => $q->total,
                ];
            })->toArray();
        dd($firstSemesters);
        $firstSemesterOneMonths = $groupChannels->tbas()->whereMonth('lecture_date', 1)
            ->groupByRaw('year(lecture_date)')->selectRaw("count(tbas.id) total,year(lecture_date) year")->get()
            ->map(function ($q) {
                return [
                    'year'  => $q->year,
                    'total' => $q->total,
                ];
            })->toArray();
        $secondSemester         = $groupChannels->tbas()->whereBetween(\DB::raw("month(lecture_date)"), [2, 7])
            ->groupByRaw('year(lecture_date)')->selectRaw("count(tbas.id) total,year(lecture_date) year")->get()
            ->map(function ($q) {
                return [
                    'year'  => $q->year,
                    'total' => $q->total,
                ];
            })->toArray();

        $mergeUpdateArr = $this->mergeUpdateArr('year', 'total', $firstSemesters, $firstSemesterOneMonths);
        dd($mergeUpdateArr, $secondSemester);

        dd($mergeUpdateArr);

        $collection = $firstSemesters->merge($firstSemesterOnMonths);
        // repeat key
        $repeatKey = $collection->duplicates('year')->first();
        // repeat value
        $repeatSum = $collection->where('year', $repeatKey)->sum('total');
        $result    = $collection->map(function ($q) use ($repeatKey, $repeatSum) {
            return [
                'year'  => $q['year'],
                'total' => $q['year'] === $repeatKey ? $repeatSum : $q['total']
            ];
        })->unique('year');
        dd($result);

//        $collection->each(function ())
//        dd($collection->duplicates('year'));
//        dd(array_search('year',$firstSemesters->toArray()));
//        dd($firstSemesters->toArray(), $firstSemesterOnMonths->toArray());

//        $firstSemesters->map(function ($v, $k) use ($firstSemesterOnMonths, &$merge) {
//            dd($firstSemesterOnMonths->firstWhere('year',2017));
//            dump($v, $firstSemesterOnMonths->whereIn('year', $v['year']));
//            if ($firstSemesterOnMonths->firstWhere('year', $v['year'])) {
//                $merge[] = array_merge($v,$firstSemesterOnMonths->firstWhere('year', $v['year']));
//            } else {
//                $merge[] = array_merge($v,$firstSemesterOnMonths->firstWhere('year', '!=', $v['year']));
//            }
//            dump($v['year']);
//            $firstSemesterOnMonths->where('year', 2017);
//            dd($v, $firstSemesterOnMonths[$k]);

//            return $merge[] = array_merge($firstSemesterOnMonths[$k], $v);
//        });
//        dd($merge);


//        dd($firstSemester, $firstSemesterOnMonth);
//        $array_merge = array_merge($firstSemester, $firstSemesterOnMonth);
//        dd($array_merge);

//        $collection     = $many->selectRaw("count(tbas.id) total,year(lecture_date) year")->get()->toArray();
//        dd($firstSemester->toArray());

        //
//        $groupChannels->semesters()->each(function ($q) use ($groupChannels, &$semester) {
//            dd($q);
//        dd($groupChannels->tbas()->where('lecture_date', '>=', '2018-0801')->where('double_green_light_status', 1)->count());
//        $semester[1]['double_green_light'] = $groupChannels->tbas()->where('lecture_date', '>=', '2018-0801')->where('double_green_light_status', 1)->count();
//        $semester[2]['total']              = $groupChannels->tbas()->where('lecture_date', '>=>', '2019-0201')->count();
//        });
        dd($semester);
    }

    public function Array()
    {
        $arr_1    = array();
        $arr_1[0] = array("year" => "2018", "total" => 14);
        $arr_1[1] = array("year" => "2019", "total" => 256);

        $arr_2    = array();
        $arr_2[0] = array("year" => "2017", "total" => 1);
        $arr_2[1] = array("year" => "2019", "total" => 6);

        $this->mergeUpdateArr('year', 'total', $arr_1, $arr_2);
    }


    private function mergeUpdateArr(string $uniqueKey, string $aggregateKey, array $originalArr, array $newArr): ?array
    {
        if (is_null($uniqueKey) || is_null($aggregateKey) || is_null($originalArr) || is_null($newArr))
            return null;

        // Create Hash
        $originalArrHash = array();
        foreach ($originalArr as $originalData) {
            $curYear                   = $originalData[$uniqueKey];
            $originalArrHash[$curYear] = $originalData;
        }

        // Update
        foreach ($newArr as $newData) {
            $curYear = $newData[$uniqueKey];
            if (array_key_exists($curYear, $originalArrHash)) {
                $originalArrHash[$curYear][$aggregateKey] += $newData[$aggregateKey];
            } else {
                $originalArrHash[$curYear] = $newData;
            }
        }
        return $originalArrHash;
    }


    public function BinarySearchKey($array = [5, 20, 8, 3], $targetNum = 4)
    {
        sort($array);
        $length      = count($array);
        $targetindex = 0;
        $left        = 0;
        $right       = 0;
        for ($right = $length - 1; $left != $right;) {
            $midIndex = ($right + $left);
            $mid      = ($right - $left);

            $midValue = $array[$midIndex];

            if ($targetNum == $midValue) {
                return $midValue;
            }

            if ($targetNum > $midValue) {
                $left = $midIndex;
            } else {
                $right = $midIndex;
            }

            if ($mid <= 1) {
                break;
            }
        }
        $rightnum = $array[$right];
        $leftnum  = $array[$left];

        return abs(($rightnum - $leftnum) / 2) > abs($rightnum - $targetNum) ? $rightnum : $leftnum;
    }


    function GetNearest($arr = [8, 2], $search = 2)
    {
        sort($arr);
        $closest = null;
        foreach ($arr as $item) {
            if ($closest === null || abs($search - $closest) > abs($item - $search)) $closest = $item;
        }
        dd($closest);

        return $closest;
    }

    public function testTwoSum()
    {
        $nums   = [3, 2, 4];
        $target = 6;
        for ($i = 0; $i < count($nums); $i++) {
            for ($j = $i + 1; $j < count($nums); $j++) {
                if ($nums[$j] == $target - $nums[$i]) {
                    return [$i, $j];
                }
            }
        }
        $this->assertTrue(True);
    }

}
