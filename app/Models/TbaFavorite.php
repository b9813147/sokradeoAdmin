<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaFavorite
 *
 * @property int $id
 * @property int $user_id
 * @property int $tba_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tba $tba
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFavorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFavorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFavorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFavorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFavorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFavorite whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFavorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaFavorite whereUserId($value)
 * @mixin \Eloquent
 */
class TbaFavorite extends Model
{
    //
    protected $fillable = [
            'user_id', 'tba_id',
    ];
    
    //
    public function tba()
    {
        return $this->belongsTo('App\Models\Tba');
    }
}
