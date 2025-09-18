<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComponentPhoto extends Model
{
    protected $table = 'components_photos';
    protected $fillable = ['component_id', 'photo_id'];
    public $timestamps = false;
}
