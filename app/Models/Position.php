<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = ['name', 'position'];

    /**
     * Получить компоненты, содержащие позицию.
     */
    public function components() : HasMany
    {
        return $this->hasMany(Component::class);
    }
}
