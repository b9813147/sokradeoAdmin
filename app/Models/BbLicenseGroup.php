<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbLicenseGroup extends Model
{
    protected $primaryKey = null;
    public $incrementing = false;

    protected $table = 'bb_license_group';
    protected $fillable = [
        'bb_license_id',
        'order_no',
        'group_id',
        'storage',
        'status',
        'start_time',
        'deadline',
    ];

    public function BbLicense(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BbLicense::class);
    }
}
