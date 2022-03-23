<?php

namespace App\Http\Controllers\Api\V1\App\School;


use App\Enums\Observation\ClassStatus;
use App\Http\Controllers\Api\V1\Controller;
use App\Libraries\Azure\Blob;
use App\Models\ObservationClass;
use App\Services\GroupSubjectFieldsService;
use App\Services\ObservationClassService;
use App\Services\RatingService;
use App\Services\TbaService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObservationClassController extends Controller
{

    protected $observationClassService;
    /**
     * @var TbaService
     */
    protected $tbaService;
    /**
     * @var TbaService
     */
    protected $groupSubjectFieldsService;
    protected $ratingService;
    protected $userService;
    protected $tagTypeService;

    public function __construct(
        ObservationClassService   $observationClassService,
        TbaService                $tbaService,
        GroupSubjectFieldsService $groupSubjectFieldsService,
        RatingService             $ratingService,
        UserService               $userService
    )
    {
        $this->observationClassService   = $observationClassService;
        $this->tbaService                = $tbaService;
        $this->groupSubjectFieldsService = $groupSubjectFieldsService;
        $this->ratingService             = $ratingService;
        $this->userService               = $userService;
    }


    /**
     * 觀譯課資訊
     * @param Request $request
     * @param string $classroom_code
     * @return JsonResponse
     */
    public function show(Request $request, string $classroom_code): JsonResponse
    {
        $request->merge(['classroom_code' => $classroom_code]);
        $request->validate([
            'classroom_code' => 'required|string|exists:App\Models\ObservationClass,classroom_code',
            'pin_code'       => 'required|string|max:4|min:4',
            'guest'          => 'string'
        ]);

        $pin_code              = $request->input('pin_code');
        $blob                  = new Blob(getenv('BLOB_ACCOUNT'), getenv('BLOB_KEY'), getenv('ENDPOINT'));
        $observationClassModel = $this->observationClassService->firstWhere(['classroom_code' => $classroom_code, 'pin_code' => $pin_code]);

        if (!$observationClassModel instanceof ObservationClass) {
            return $this->setStatus(422)->respond('pin_code verification failed');
        }
        $storageLimit = $observationClassModel->group->bblicenses->where('code', 'LL9MJ6NY')->max('pivot.storage') ?? 0;

        $data = [
            'sas'            => null,
            'url'            => null,
            'binding_number' => null,
            'status'         => 1,
        ];
        // 檢查線數量
        if ($observationClassModel->observationUsers->count() <= $storageLimit) {
            if (auth()->check()) {
                $observationClassModel->observationUsers()->updateOrCreate(['user_id' => auth()->id()], ['user_id' => auth()->id()]);
            } else {
                $observationClassModel->observationUsers()->updateOrCreate(['guest' => $request->input('guest')], ['guest' => $request->input('guest')]);
            }
            // 檢查狀態
            if ($observationClassModel->status !== ClassStatus::END) {
                $data = [
                    'sas'            => $blob->getSas('', getenv('BLOB_VIDEO_CONTAINER'), 1, 'wlcr', 'c'),
                    'url'            => $blob->getContainerUrl(getenv('BLOB_VIDEO_CONTAINER')) . '/observation',
                    'binding_number' => $observationClassModel->binding_number,
                    'status'         => 0,
                ];
            }
        }

        return $this->success($data);
    }
}
