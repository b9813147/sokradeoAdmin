<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ConfigParameter
 *
 * @property string $cate
 * @property string $attr
 * @property string|null $val
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConfigParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConfigParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConfigParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConfigParameter whereAttr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConfigParameter whereCate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConfigParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConfigParameter whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConfigParameter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConfigParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ConfigParameter whereVal($value)
 * @mixin \Eloquent
 */
class ConfigParameter extends Model
{
    //
}
