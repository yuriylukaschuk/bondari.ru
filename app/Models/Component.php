<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enum\TagType;


class Component extends Model
{
    protected $table = 'components';
    protected $fillable = ['block_id', 'tag_id', 'position_id', 'number'];

    /**
     * Получить блок, которому принадлежит компонент.
     */
    public function blocks(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    /**
     * Получить тег, которому принадлежит компонент.
     */
    public function tags(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }

    /**
     * Получить позицию, назначенную компоненту.
     */
    public function positions(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Получить содержимое, относящееся к данному компоненту.
     */
    public function contents() : HasOne
    {
        return $this->HasOne(Content::class);
    }

    /**
     * Получить фотографии, относящееся к данному компоненту.
     */
    public function photos() : BelongsToMany
    {
        return $this->BelongsToMany(Photo::class, 'components_photos', 'component_id', 'photo_id');
    }


    public function isMainTitle()
    {
        return $this->tag_id == TagType::MainTitle->value;
    }

    public function isSubTitle()
    {
        return $this->tag_id == TagType::SubTitle->value;
    }

    public function isLargeText()
    {
        return $this->tag_id == TagType::LargeText->value;
    }

    public function isSmallText()
    {
        return $this->tag_id == TagType::SmallText->value;
    }

    public function isNumberedList()
    {
        return $this->tag_id == TagType::NumberedList->value;
    }

    public function isBulletedList()
    {
        return $this->tag_id == TagType::BulletedList->value;
    }

    public function isPhoto()
    {
        return $this->tag_id == TagType::Photo->value;
    }

    public function isPhotoSet()
    {
        return $this->tag_id == TagType::PhotoSet->value;
    }


    public function isText()
    {
        return in_array($this->tag_id, [
            TagType::MainTitle->value,
            TagType::SubTitle->value,
            TagType::LargeText->value,
            TagType::SmallText->value,
            TagType::NumberedList->value,
            TagType::BulletedList->value
        ]);
    }

    public function getContent()
    {
        if ($this->isPhoto()) {
            return $this->photos->first();
        } elseif ($this->isPhotoSet()) {
            return $this->photos->first();
        } elseif ($this->isText()) {
            return $this->contents;
        }
        return null;
    }

    public static function getComponents($block_id)
    {
        //DB::enableQueryLog();
        return Component::where('block_id','=',$block_id)
            ->orderBy('npp')
            ->get();
        //$queries = DB::getQueryLog();
        //file_put_contents('/var/www/bondari.com/tests/getComponents', 'sql = '.print_r($queries, true), FILE_APPEND);
        //return $components;
    }

    public static function getOne($id)
    {
        // DB::enableQueryLog();
        return Component::where('id','=',$id)->first();
        //$queries = DB::getQueryLog();
        //file_put_contents('/var/www/bondari.com/tests/getQueryLog', 'sql = '.print_r($queries, true), FILE_APPEND);
    }

    public static function addComponents($params = [])
    {
        file_put_contents('/var/www/bondari.com/tests/addComponents', 'params = '.print_r($params, true), FILE_APPEND);
        DB::enableQueryLog();
        $result = Component::create($params);
        $queries = DB::getQueryLog();
        file_put_contents('/var/www/bondari.com/tests/addComponents', 'sql = '.print_r($queries, true), FILE_APPEND);
        return $result->id;
    }

    public static function updateComponents($id, $params = [])
    {
        DB::enableQueryLog();
        Component::where('id','=',$id)->update($params);
        $queries = DB::getQueryLog();
        file_put_contents('/var/www/bondari.com/tests/updateComponents', 'sql = '.print_r($queries, true), FILE_APPEND);
    }

}
