<?php

namespace App\Http\Controllers\Api\V1\District;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\DistrictSubjectRequest;
use App\Http\Requests\DistrictSubjectUpdateRequest;
use App\Http\Resources\DistrictSubjectCollection;
use App\Services\DistrictGroupSubjectService;
use App\Services\DistrictSubjectService;
use App\Services\RecordService;
use App\Types\Record\RecordType;
use Illuminate\Http\Request;

class DistrictSubjectController extends Controller
{
    /**
     * @var DistrictSubjectService
     */
    protected $districtSubjectService;
    /**
     * @var DistrictGroupSubjectService
     */
    protected $districtGroupSubjectService;
    protected $recordService;

    /**
     * DistrictSubjectController constructor.
     * @param DistrictSubjectService $districtSubjectService
     * @param DistrictGroupSubjectService $districtGroupSubjectService
     * @param RecordService $recordService
     */
    public function __construct(
        DistrictSubjectService      $districtSubjectService,
        DistrictGroupSubjectService $districtGroupSubjectService,
        RecordService               $recordService

    )
    {
        $this->districtSubjectService      = $districtSubjectService;
        $this->districtGroupSubjectService = $districtGroupSubjectService;
        $this->recordService               = $recordService;
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DistrictSubjectRequest $request): \Illuminate\Http\JsonResponse
    {
        $only = $request->only('districts_id', 'subject', 'alias', 'order');

        try {
            $model = $this->districtSubjectService->create($only);
            // 操作紀錄
            $this->recordService->create([
                'type'                => RecordType::CREATE,
                'user_id'             => auth()->id(),
                'district_subject_id' => $model->id,
                'district_id'         => $request->input('districts_id')
            ]);
        } catch (\Exception $exception) {

            \Log::error($exception->getMessage());
            return $this->setStatus(422)->fail($exception->getCode());
        }

        return $this->setStatus(204)->success(null);

    }

    /**
     * Display the specified resource.
     *
     * @param int $districtId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $districtId): \Illuminate\Http\JsonResponse
    {
        $districtSubjectInfo = $this->districtSubjectService->getDistrictSubjectList($districtId, 'ASC');

        return $this->success(new DistrictSubjectCollection($districtSubjectInfo));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param DistrictSubjectUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DistrictSubjectUpdateRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $new_order = $request->input('order');
            if ($this->districtSubjectService->exists(['id' => $id])) {
                $info = $this->districtSubjectService->find($id);
                $this->districtSubjectService->adjustSort('update', ['districts_id' => $info->districts_id], 'order', $info->order, $new_order);
                $this->districtSubjectService->update($id, $request->all());
            }

            //操作紀錄
            $this->recordService->create([
                'type'                => RecordType::UPDATE,
                'user_id'             => auth()->id(),
                'district_subject_id' => $id,
            ]);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return $this->setStatus(422)->fail($exception->getCode());
        }

        return $this->setStatus(204)->success(null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        if ($this->districtSubjectService->exists(['id' => $id])) {
            $info = $this->districtSubjectService->find($id);
            $this->districtSubjectService->adjustSort('destroy', ['districts_id' => $info->districts_id], 'order', $info->order, 1);
            $this->districtSubjectService->destroy($id);

            // 操作紀錄
            $this->recordService->create([
                'type'                => RecordType::DELETE,
                'user_id'             => auth()->id(),
                'district_subject_id' => $id,
            ]);
            return $this->setStatus(204)->success(null);
        }
        return $this->setStatus(422)->fail('Do not delete during use');
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSort(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'order' => 'required|integer'
        ]);

        $new_order = $request->input('order');
        try {
            if ($this->districtSubjectService->exists(['id' => $id])) {
                $info = $this->districtSubjectService->find($id);
                $this->districtSubjectService->adjustSort('update', ['districts_id' => $info->districts_id], 'order', $info->order, $new_order);
                $this->districtSubjectService->updateBy('id', $id, [
                    'order' => $new_order,
                ]);
            }
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return $this->setStatus(422)->fail($exception->getCode());
        }

        return $this->setStatus(204)->success(null);
    }
}
