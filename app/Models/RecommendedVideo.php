<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RecommendedVideo
 *
 * @property int $id
 * @property int $tba_id 影片 ID
 * @property int $group_channel_id 影片來源頻道
 * @property int|null $district_id 由學區內推薦時使用
 * @property int|null $group_id 由頻道內推薦時使用
 * @property int|null $order 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RecommendedVideo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecommendedVideo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecommendedVideo query()
 * @method static \Illuminate\Database\Eloquent\Builder|RecommendedVideo whereGroupChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecommendedVideo whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecommendedVideo whereDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecommendedVideo whereId($value)
 * @mixin \Eloquent
 */
class RecommendedVideo extends Model
{
    protected $fillable = ['tba_id','group_channel_id','district_id','group_id','order'];

    //
    public function tba()
    {
        return $this->belongsTo(Tba::Class, 'tba_id', 'id');
    }

    //
    public function groupChannel()
    {
        return $this->belongsTo(GroupChannel::Class, 'group_channel_id', 'id');
    }
}
