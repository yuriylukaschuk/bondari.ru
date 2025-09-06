<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsToMany;

class Content extends Model
{
    protected $table = 'contents';
    protected $primaryKey = 'component_id';
    public $incrementing = false;
    protected $fillable = ['component_id','content'];
    public $timestamps = true;

    protected $casts = [
        'content' => 'array' // Автоматическое преобразование JSON ↔ array
    ];
    
    public function components() : belongsToMany
    {
        return $this->belongsToMany(Component::class, 'components_contents', 'content_id', 'component_id');
    }

}
