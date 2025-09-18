<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    protected $table = 'blocks';
    protected $fillable = ['razdel_id', 'npp', 'images', 'image_position', 'imageset', 'positions'];

    /**
     * Получить раздел, которому принадлежит блок.
     */
    public function razdel(): BelongsTo
    {
        return $this->belongsTo(Razdel::class);
    }

    /**
     * Получить компоненты, включенные в блок.
     */
    public function components() : HasMany
    {
        return $this->hasMany(Component::class, 'block_id');
    }

    public static function decrementBlocks($id, $fields)
    {
        foreach ($fields as $key => $val){
            Block::where('id','=',$id)->decrement($key, $val);
        }
    }

    public static function incrementBlocks($id, $fields)
    {
        foreach ($fields as $key => $val){
            Block::where('id','=',$id)->increment($key, $val);
        }
    }
}
