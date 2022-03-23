<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaStatisticLog
 *
 * @property int $id
 * @property int $user_id 使用者ID
 * @property int $tba_id 影片
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaStatisticLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaStatisticLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaStatisticLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaStatisticLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaStatisticLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaStatisticLog whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaStatisticLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaStatisticLog whereUserId($value)
 * @mixin \Eloquent
 */
class TbaStatisticLog extends Model
{
    protected $table = 'tba_statistic_logs';
    protected $fillable = [
        'user_id',
        'tba_id'
    ];
}
