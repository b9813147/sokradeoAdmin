<?php

namespace App\Http\Controllers\Api\V1\Groups;


use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\SubjectRequest;
use App\Http\Resources\GroupSubjectFieldCollection;
use App\Http\Resources\SubjectFieldCollection;
use App\Services\DistrictGroupSubjectService;
use App\Services\GroupSubjectFieldsService;
use App\Services\RecordService;
use App\Services\SubjectFieldsService;

use App\Types\Record\RecordType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SubjectController extends Controller
{
    /**
     * @var SubjectFieldsService
     */
    protected $subjectFieldsService;
    /**
     * @var GroupSubjectFieldsService
     */
    protected $groupSubjectFieldsService;

    /**
     * @var DistrictGroupSubjectService
     */
    protected $districtGroupSubjectService;
    protected $recordService;

    /**
     * SubjectController constructor.
     * @param SubjectFieldsService $subjectFieldsService
     * @param GroupSubjectFieldsService $groupSubjectFieldsService
     * @param DistrictGroupSubjectService $districtGroupSubjectService
     * @param RecordService $recordService
     */
    public function __construct(
        SubjectFieldsService        $subjectFieldsService,
        GroupSubjectFieldsService   $groupSubjectFieldsService,
        DistrictGroupSubjectService $districtGroupSubjectService,
        RecordService               $recordService
    )
    {
        $this->subjectFieldsService        = $subjectFieldsService;
        $this->groupSubjectFieldsService   = $groupSubjectFieldsService;
        $this->districtGroupSubjectService = $districtGroupSubjectService;
        $this->recordService               = $recordService;
    }

    /**
     * @param int $group_id
     * @return mixed
     */
    public function show(int $group_id)
    {
        $data = [];

        $data['subjects'] = new GroupSubjectFieldCollection($this->groupSubjectFieldsService->getSubject($group_id));
        $data['area']     = new SubjectFieldCollection($this->subjectFieldsService->getAreaLang());

        return $this->success($data);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse
    {

        $only      = $request->only('subject_fields_id', 'alias', 'order');
        $new_order = $only['order'];
        try {
            if ($this->groupSubjectFieldsService->exists(['id' => $id])) {
                $info = $this->groupSubjectFieldsService->find($id);
                $this->groupSubjectFieldsService->adjustSort('update', ['groups_id' => $info->groups_id], 'order', $info->order, $new_order);
                $this->groupSubjectFieldsService->update($id, $only);
            }
            // 操作紀錄
            $this->recordService->create([
                'type'                   => RecordType::UPDATE,
                'user_id'                => auth()->id(),
                'group_subject_field_id' => $id,
            ]);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return $this->setStatus(422)->fail($exception->getCode());
        }

        return $this->setStatus(204)->success(null);
    }

    /**
     * @param SubjectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SubjectRequest $request): \Illuminate\Http\JsonResponse
    {
        $only = $request->only('subject', 'groups_id', 'subject_fields_id', 'alias', 'order');

        try {
            $model = $this->groupSubjectFieldsService->create($only);
            // 操作紀錄
            $this->recordService->create([
                'type'                   => RecordType::CREATE,
                'user_id'                => auth()->id(),
                'group_subject_field_id' => $model->id,
                'group_id'               => $request->input('group_id'),
            ]);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return $this->setStatus(422)->fail($exception->getCode());
        }
        return $this->setStatus(204)->success(null);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        if ($this->groupSubjectFieldsService->exists(['id' => $id])) {
            // 刪除學區學科
            $this->districtGroupSubjectService->deleteWhere(['group_subject_fields_id' => $id]);
            $info = $this->groupSubjectFieldsService->find($id);
            $this->groupSubjectFieldsService->adjustSort('destroy', ['groups_id' => $info->groups_id], 'order', $info->order, 1);
            $this->groupSubjectFieldsService->destroy($id);

            // 操作記錄
            $this->recordService->create([
                'type'                   => RecordType::DELETE,
                'user_id'                => auth()->id(),
                'group_subject_field_id' => $id,
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
            if ($this->groupSubjectFieldsService->exists(['id' => $id])) {
                $info = $this->groupSubjectFieldsService->find($id);
                $this->groupSubjectFieldsService->adjustSort('update', ['groups_id' => $info->groups_id], 'order', $info->order, $new_order);
                $this->groupSubjectFieldsService->updateBy('id', $id, [
                    'order' => $new_order,
                ]);
            }
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return $this->setStatus(422)->fail($exception->getCode());
        }

        return $this->setStatus(204)->success('200');
    }
}
