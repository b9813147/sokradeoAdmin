<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaEvaluateUser
 *
 * @property int $id
 * @property int $tba_id
 * @property int $user_id
 * @property string|null $identity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tba $tba
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TbaEvaluateEvent[] $tbaEvaluateEvents
 * @property-read int|null $tba_evaluate_events_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaEvaluateUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaEvaluateUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaEvaluateUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaEvaluateUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaEvaluateUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaEvaluateUser whereIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaEvaluateUser whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaEvaluateUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaEvaluateUser whereUserId($value)
 * @mixin \Eloquent
 */
class TbaEvaluateUser extends Model
{
    //
    protected $fillable = [
            'tba_id', 'user_id', 'identity',
    ];
    
    //
    public function tba()
    {
        return $this->belongsTo('App\Models\Tba');
    }
    
    //
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
    //
    public function tbaEvaluateEvents()
    {
        return $this->hasMany('App\Models\TbaEvaluateEvent');
    }
    
}
