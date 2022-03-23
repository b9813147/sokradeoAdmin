<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagType extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['content', 'group_id', 'user_id', 'id', 'status'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'tag_types';

    public function tags(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tag::class, 'type_id', 'id');
    }
}
