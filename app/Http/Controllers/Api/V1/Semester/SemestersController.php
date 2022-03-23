<?php

namespace App\Http\Controllers\Api\V1\Semester;

use App\Http\Controllers\Api\V1\Controller;
use App\Models\Semester;
use App\Services\SemestersService;
use Illuminate\Http\Request;

class SemestersController extends Controller
{
    /**
     * @var SemestersService
     */
    protected $SemesterService;

    /**
     * SemestersController constructor.
     * @param SemestersService $SemestersService
     */
    public function __construct(SemestersService $SemestersService)
    {
        $this->SemesterService = $SemestersService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $group_id
     * @return \Illuminate\Http\Response
     */
    public function show(int $group_id)
    {
        $this->SemesterService->findBy('group_id', $group_id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Semester $semester
     * @return \Illuminate\Http\Response
     */
    public function edit(Semester $semester)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Semester $semester
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Semester $semester)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Semester $semester
     * @return \Illuminate\Http\Response
     */
    public function destroy(Semester $semester)
    {
        //
    }
}
