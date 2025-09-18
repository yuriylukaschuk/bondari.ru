<?php

namespace Database\Seeders;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// php artisan db:seed --class=RoleSeeder

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $developer = new Role();
        $developer->name = 'Разработчик';
        $developer->slug = 'web-developer';
        $developer->save();

        $manager = new Role();
        $manager->name = 'Поользователь сайта';
        $manager->slug = 'user';
        $manager->save();
    }
}
