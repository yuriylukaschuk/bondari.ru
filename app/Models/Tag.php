<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    protected $fillable = ['name', 'class'];

    /**
     * Получить компоненты, содержащие тег.
     */
    public function components() : HasMany
    {
        return $this->hasMany(Component::class, 'tag_id');
    }

}
