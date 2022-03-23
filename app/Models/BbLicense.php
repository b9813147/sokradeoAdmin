<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BbLicense extends Model
{
    protected $fillable = ['name', 'code'];

    public function bbLicenseGroups(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BbLicenseGroup::class);
    }
}
