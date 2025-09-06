<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// php artisan db:seed --class=PositionSeeder

class PositionSeeder extends Seeder
{
    public $positions = [
        ['Текст слева', 'text-left'],
        ['Текст по центру', 'text-center'],
        ['Текст справа', 'text-right'],
        ['Текст по ширине', 'text-justify'],
        ['Фото слева', 'photo-left'],
        ['Фото по центру', 'photo-center'],
        ['Фото справа', 'photo-right'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         foreach ($this->positions as $key => $val){
            $request = [
                'name' => $val[0],
                'position' => $val[1],
            ];
            DB::table('positions')->insert($request);
        }
    }
}
