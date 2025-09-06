<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RazdelImage extends Model
{
    protected $fillable = ['parent_id', 'lvl', 'npp', 'url', 'title', 'description', 'keywords', 'feedback_title'];
}
