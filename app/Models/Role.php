<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{

    protected $fillable = ['name', 'slug'];

    public function permissions() : BelongsToMany
    {
        return $this->belongsToMany(Permission::class,'roles_permissions', 'role_id', 'permission_id');
    }
}
