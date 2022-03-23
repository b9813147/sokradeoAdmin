<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\Models\ClientObserver;

/**
 * App\Models\Client
 *
 * @property string $id
 * @property string $type
 * @property string $name
 * @property string $secret
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Client extends Model
{
    public    $incrementing = false;
    
    protected $keyType      = 'string';
    
    protected $fillable = [
            'type', 'name', 'secret',
    ];
    
    public static function boot()
    {
        parent::boot();
        self::observe(ClientObserver::class);
    }
}
