<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TbaAnalysisEvent
 *
 * @property int $id
 * @property int $tba_id
 * @property int $tba_analysis_event_mode_id
 * @property float|null $time_start
 * @property float|null $time_end
 * @property float|null $time_point
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tba $tba
 * @property-read \App\Models\TbaAnalysisEventMode $tbaAnalysisEventMode
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnalysisEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnalysisEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnalysisEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnalysisEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnalysisEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnalysisEvent whereTbaAnalysisEventModeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnalysisEvent whereTbaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnalysisEvent whereTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnalysisEvent whereTimePoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnalysisEvent whereTimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TbaAnalysisEvent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TbaAnalysisEvent extends Model
{
    //
    protected $fillable = [
            'tba_id', 'tba_analysis_event_mode_id', 'time_start', 'time_end', 'time_point',
    ];
    
    //
    public function tba()
    {
        return $this->belongsTo('App\Models\Tba');
    }
    
    //
    public function tbaAnalysisEventMode()
    {
        return $this->belongsTo('App\Models\TbaAnalysisEventMode');
    }
}
