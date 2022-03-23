<?php

namespace App\Repositories;

use App\Models\ConfigParameter;
use Illuminate\Database\Query\Builder;

class ConfigRepository extends BaseRepository
{
    /**
     * @var ConfigParameter
     * @var $model Builder
     */
    protected $model;

    public function __construct(ConfigParameter $configParameter)
    {
        $this->model = $configParameter;
    }


    /**
     * @param string $cate
     * @return array
     */
    public function getParamsByCate($cate)
    {
        $result    = [];
        $parameter = $this->getBy('cate', $cate);

        $parameter->each(function ($v) use (& $result) {
            $result[$v->attr] = $v;
        });

        return $result;
    }

    /**
     * @param string $cate
     * @param string $attr
     * @param null $val
     */
    public function setParamVal($cate, $attr, $val = null)
    {
        ConfigParameter::query()->where('cate', $cate)->where('attr', $attr)->update(['val' => $val]);
    }
}
