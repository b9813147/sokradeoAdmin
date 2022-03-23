<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObservationUser extends Model
{
    protected $fillable = ['user_id', 'observation_id','guest'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
