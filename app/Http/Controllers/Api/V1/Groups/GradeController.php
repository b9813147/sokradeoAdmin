<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\GradeCollection;
use App\Libraries\Lang\Lang;
use App\Services\GradeLangService;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * @var GradeLangService
     */
    protected $gradeLangService;

    /**
     * GradeController constructor.
     * @param GradeLangService $gradeLangService
     */
    public function __construct(GradeLangService $gradeLangService)
    {
        $this->gradeLangService = $gradeLangService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return GradeCollection
     */
    public function index()
    {
        $locales_id = Lang::getConvertByLangStringForId();

        $result = $this->gradeLangService->getBy('locales_id', $locales_id);
        GradeCollection::wrap('grades');

        return new GradeCollection($result);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
