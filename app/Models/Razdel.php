<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Razdel extends Model
{
    protected $table = 'razdels';
    protected $fillable = ['parent_id', 'lvl', 'npp', 'url', 'title', 'description', 'keywords', 'feedback_title'];

    /**
     * Получить блоки, относящиеся к выбранному разделу
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'razdel_id');
    }

    /**
     * Для древовидной структуры
     */
    public function parent()
    {
        return $this->belongsTo(Razdel::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Razdel::class, 'parent_id');
    }

    public static function allChildrens(int $parent_id = 0)
    {
        return DB::select(
            'WITH RECURSIVE cte AS (
                SELECT * FROM razdels WHERE id = ?
                UNION ALL
                SELECT c.* FROM razdels c JOIN cte ON c.parent_id = cte.id
            )
            SELECT * FROM cte;',
            [$parent_id]
        );
    }

    public static function firstParent(int $parent_id = 0)
    {

        return DB::select(
            'WITH RECURSIVE Ancestors AS (
                -- Начальная точка: выбираем корневой узел
                SELECT
                    id,
                    parent_id,
                    url,
                    1 AS lvl
                FROM
                    razdels
                WHERE
                    id = ?
                UNION ALL
                SELECT
                    t.id,
                    t.parent_id,
                    t.url,
                    a.lvl + 1
                FROM
                    razdels t
                JOIN
                    Ancestors a ON t.parent_id = a.id
                WHERE
                    a.lvl < ?
            )
            SELECT
                id,
                parent_id,
                url
            FROM
                Ancestors
            WHERE
                lvl = 1;',
            [$parent_id]
        );
    }
}
