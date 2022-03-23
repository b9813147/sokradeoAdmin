<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EducationalStage
 *
 * @property int $id
 * @property string $type
 * @property int $total_grade
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tba[] $tbas
 * @property-read int|null $tbas_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducationalStage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducationalStage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducationalStage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducationalStage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducationalStage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducationalStage whereTotalGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducationalStage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EducationalStage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EducationalStage extends Model
{
    protected $fillable = ['type','total_grade'];
    protected $primaryKey = 'id';
    protected $table = 'educational_stages';

    public function tbas()
    {
        return $this->hasMany(Tba::class);
    }
}
