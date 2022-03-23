<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DistrictSubject
 *
 * @property int $id
 * @property string|null $subject 學科名稱
 * @property string|null $alias 學科別名
 * @property int|null $order 排序
 * @property int $districts_id
 * @property int|null $subject_fields_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DistrictGroupSubject[] $districtGroupSubject
 * @property-read int|null $district_group_subject_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictSubject whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictSubject whereDistrictsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictSubject whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictSubject whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictSubject whereSubjectFieldsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DistrictSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DistrictSubject extends Model
{
    protected $fillable = ['subject', 'alias', 'order', 'districts_id', 'subject_fields_id'];

    public function districtGroupSubject()
    {
//        return $this->belongsToMany(DistrictSubject::class, DistrictGroupSubject::class)->withPivot('group_subject_fields_id');
        return $this->hasMany(DistrictGroupSubject::class, 'district_subjects_id', 'id');
    }
}
