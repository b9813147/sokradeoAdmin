<?php

namespace App\Http\Controllers\Api\V1\District;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\DistrictRatingCollection;
use App\Http\Resources\DistrictRatingResource;
use App\Http\Resources\RatingCollection;
use App\Services\RatingService;
use App\Services\RecordService;
use App\Types\Record\RecordType;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * @var RatingService
     */
    protected $ratingService;
    protected $recordService;

    /**
     * RatingController constructor.
     * @param RatingService $ratingService
     * @param $recordService
     */
    public function __construct(RatingService $ratingService, RecordService $recordService)
    {
        $this->ratingService = $ratingService;
        $this->recordService = $recordService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $districtId
     * @return void
     */
    public function show(int $districtId)
    {
        $ratingInfo = $this->ratingService->getByUseStatistics(['districts_id' => $districtId], 'ASC');

        return response()->json(new DistrictRatingCollection($ratingInfo), 200);
    }
}
