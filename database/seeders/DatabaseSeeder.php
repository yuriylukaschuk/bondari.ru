<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Razdel;
use App\Models\Position;
use App\Models\Tag;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// php artisan db:seed

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(PositionSeeder::class);
        $this->call(RazdelSeeder::class);
        $this->call(TagSeeder::class);
    }
}
