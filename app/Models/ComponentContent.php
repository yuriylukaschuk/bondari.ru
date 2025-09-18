<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComponentContent extends Model
{
    protected $table = 'components_contents';
    protected $fillable = ['component_id', 'content_id'];
    public $timestamps = false;
}
