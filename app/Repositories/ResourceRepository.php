<?php

namespace App\Repositories;

use App\Models\Resource;
use App\Types\Src\SrcType;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;

class ResourceRepository extends BaseRepository
{
    /**
     * @var Resource
     * @var $model Builder
     */
    protected $model;

    /**
     * ResourceRepository constructor.
     * @param Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->model = $resource;
    }

    public function createResrc($userId, $resrc)
    {
        if (!SrcType::check($resrc['src_type'])) {
            throw new InvalidArgumentException('src type is illegal');
        }

        $resrc['user_id'] = $userId;
        return $this->model->query()->updateOrCreate($resrc, $resrc);
    }

    public function setResrc($resrcId, $resrcData)
    {
        $resrc = $this->model->query()->findOrFail($resrcId);
        $resrc->fill($resrcData);
        $resrc->save();
    }
}
