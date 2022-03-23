<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictLang
 *
 * @property int $id
 * @property int $districts_id
 * @property int $locales_id
 * @property string|null $name 學區名稱
 * @property string|null $description 學區描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictLang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictLang whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictLang whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictLang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictLang whereLocalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictLang whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictLang whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DistrictLang extends Model
{
    protected $table = 'district_langs';
    protected $primaryKey = 'id';
    protected $fillable = ['locales_id', 'name', 'description', 'districts_id'];


    public function districts()
    {
        return $this->belongsTo(Districts::class);
    }

    public function locales()
    {
        return $this->hasMany(Locale::class);
    }
}
