<?php

namespace App\Repositories;

use App\Models\TbaAnnex;
use Illuminate\Database\Query\Builder;

class TbaAnnexRepository extends BaseRepository
{
    /**
     * @var TbaAnnex
     * @var $model Builder
     */
    protected $model;

    public function __construct(TbaAnnex $tbaAnnex)
    {
        $this->model = $tbaAnnex;
    }

    //
    public function getAnnexes($tbaId, $conds = [])
    {
        return $this->model->query()->where('tba_id', $tbaId)->where($conds)->get();
    }

    //
    public function getAnnex($annexId)
    {
        return $this->model->query()->with('resource')->findOrFail($annexId);
    }

    //
    public function getAnnexResrcs($tbaId, $conds = [])
    {
        return $this->model->query()
            ->join('resources', 'tba_annexes.resource_id', '=', 'resources.id')
            ->where('tba_id', $tbaId)
            ->where($conds)
            ->select('tba_annexes.*', 'resources.user_id', 'resources.src_type', 'resources.name')
            ->get();
//        return DB::table('tba_annexes')
//            ->where('tba_id', $tbaId)
//            ->join('resources', 'tba_annexes.resource_id', '=', 'resources.id')
//            ->where($conds)
//            ->select('tba_annexes.*', 'resources.user_id', 'resources.src_type', 'resources.name')
//            ->get();

    }

    //
//    public function getAnnexResrc($annexId)
//    {
//        return DB::table('tba_annexes')
//            ->where('tba_annexes.id', $annexId)
////            ->where('tba_id', $tbaId)
//            ->join('resources', 'tba_annexes.resource_id', '=', 'resources.id')
////            ->where($conds)
//            ->select('tba_annexes.*', 'resources.user_id', 'resources.src_type', 'resources.name')
//            ->get();
//    }

    //
    public function createAnnex($tbaId, &$annex)
    {
        $annex['tba_id'] = $tbaId;
        return $this->model->query()->updateOrCreate($annex, $annex);
    }

    //
    public function getResrc($annexId)
    {
        return $this->model->query()->findOrFail($annexId)->resource;
    }
}
