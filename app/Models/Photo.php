<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $table = 'photos';
    protected $fillable = ['razdel_id', 'filename', 'thumbnail', 'title', 'description'];

    public function components() : belongsToMany
    {
        return $this->belongsToMany(Component::class, 'components_photos', 'photo_id', 'component_id');
    }

    //  Если путь к изображению хранится в базе данных как относительный, то можно добавить аксессор для формирования полного URL.
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    // Полный URL к изображению
    public function getUrl()
    {
        return Storage::url($this->filename);
    }

    // Полный URL к миниатюре
    public function getThumbnailUrl()
    {
        return $this->thumbnail ? Storage::url($this->thumbnail) : $this->getUrl();
    }

    // Удаление файлов при удалении записи
    protected static function boot()
    {
        parent::boot();

        static::deleting(function($image) {
            Storage::delete([$image->filename, $image->thumbnail]);
        });
    }

}
