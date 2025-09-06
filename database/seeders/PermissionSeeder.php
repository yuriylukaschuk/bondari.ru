<?php

namespace Database\Seeders;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// php artisan db:seed --class=PermissionSeeder

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manageUser = new Permission();
        $manageUser->name = 'Регистрация пользователей';
        $manageUser->slug = 'manage-users';
        $manageUser->save();

        $createTasks = new Permission();
        $createTasks->name = 'Вход в панель управления сайтом';
        $createTasks->slug = 'dashboard-login';
        $createTasks->save();

        $createTasks = new Permission();
        $createTasks->name = 'Внесение изменений через панель управления сайтом';
        $createTasks->slug = 'dashboard-changes';
        $createTasks->save();

        $createTasks = new Permission();
        $createTasks->name = 'Просмотр настройки в панели управления сайтом';
        $createTasks->slug = 'dashboard-view';
        $createTasks->save();


    }
}
