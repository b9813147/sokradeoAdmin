<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubjectField
 *
 * @property int $id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SubjectFieldLang[] $subjectLang
 * @property-read int|null $subject_lang_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectField query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubjectField whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubjectField extends Model
{
    //
    protected $fillable = [
        'type'
    ];

    public function subjectLang()
    {
        return $this->hasMany(SubjectFieldLang::class, 'subject_fields_id', 'id');
    }
}
