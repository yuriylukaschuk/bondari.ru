<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// php artisan db:seed --class=UserSeeder

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $developer = Role::where('slug','web-developer')->first();
        $user = Role::where('slug', 'user')->first();
        $manageUsers = Permission::where('slug','manage-users')->first();
        $dashboardLogin = Permission::where('slug','dashboard-login')->first();
        $dashboardChanges = Permission::where('slug','dashboard-changes')->first();
        $dashboardView = Permission::where('slug','dashboard-view')->first();

        $user1 = new User();
        $user1->name = 'Администратор';
        $user1->email = 'admin@gmail.com';
        $user1->password = bcrypt('admin@gmail.com');
        $user1->save();
        $user1->roles()->attach($developer);
        $user1->permissions()->attach($manageUsers);
        $user1->permissions()->attach($dashboardLogin);
        $user1->permissions()->attach($dashboardChanges);


        $user2 = new User();
        $user2->name = 'Пользователь';
        $user2->email = 'user@gmail.com';
        $user2->password = bcrypt('user@gmail.com');
        $user2->save();
        $user2->roles()->attach($user);
        $user2->permissions()->attach($manageUsers);
        $user2->permissions()->attach($dashboardLogin);
        $user2->permissions()->attach($dashboardView);

    }
}
