<?php

namespace App\Repositories;

use App\Libraries\Lang\Lang;
use App\Models\SubjectField;
use Illuminate\Database\Query\Builder;

class SubjectFieldsRepository extends BaseRepository
{
    /**
     * @var SubjectField
     * @var $model Builder
     */
    protected $model;

    /**
     * SubjectFieldsRepository constructor.
     * @param $model
     */
    public function __construct(SubjectField $model)
    {
        $this->model = $model;
    }

    public function getAreaLang()
    {
        $local_id = Lang::getConvertByLangStringForId();

        return $this->model->query()->with([
            'subjectLang' => function ($q) use ($local_id) {
                return $q->where('locales_id', $local_id);
            }
        ])->get();
    }
}
