<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\QrCode
 *
 * @property int $id
 * @property string $url
 * @property int $group_id
 * @property int $status
 * @property string $duty
 * @property string $end_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QrCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QrCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QrCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QrCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QrCode whereDuty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QrCode whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QrCode whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QrCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QrCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QrCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QrCode whereUrl($value)
 * @mixin \Eloquent
 */
class QrCode extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'url',
        'group_id',
        'status',
        'duty',
        'end_time',
    ];
}
