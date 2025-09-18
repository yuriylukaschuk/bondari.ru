<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class,'roles_permissions', 'permission_id', 'role_id');
    }
}
