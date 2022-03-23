<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GradeLang
 *
 * @property int $id
 * @property int $grades_id
 * @property int $locales_id
 * @property string|null $name 年級
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Grade $grades
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GradeLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GradeLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GradeLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GradeLang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GradeLang whereGradesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GradeLang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GradeLang whereLocalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GradeLang whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GradeLang whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GradeLang extends Model
{
    protected $fillable = ['name', 'locales_id', 'grades_id'];

    public function grades()
    {
        return $this->belongsTo(Grade::class, 'id', 'grades_id');
    }
}
