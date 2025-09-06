<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// php artisan db:seed --class=TagSeeder

class TagSeeder extends Seeder
{

    public $tags = [
        ['Заголовок основной', 'title-main'],
        ['Заголовок вспомогательный', 'title-sub'],
        ['Текст большой', 'text-large'],
        ['Текст маленький', 'text-small'],
        ['Нумерованный список', 'list-numbered'],
        ['Маркированный список', 'list-bulleted'],
        ['Фотография', 'photo'],
        ['Ряд фотографий', 'photo-set'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         foreach ($this->tags as $key => $val){
            $request = [
                'name' => $val[0],
                'class' => $val[1]
            ];
            DB::table('tags')->insert($request);
        }
    }

}
