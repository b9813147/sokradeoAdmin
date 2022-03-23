<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Grade
 *
 * @property int $id
 * @property string $name 年級
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GradeLang $gradeLang
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Grade whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Grade whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Grade extends Model
{
    protected $fillable = ['name', 'id'];

    public function gradeLang()
    {
        return $this->hasOne(GradeLang::class,'grades_id','id');
    }
}
