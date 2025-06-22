<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\ImportThesesSeeder;
use Database\Seeders\ImportReservedThesisTitlesSeeder;
use Database\Seeders\ImportThesisTitlesSimpleSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\SpecializationsFromFoldersSeeder; // إضافة استخدام Seeder التخصصات الجديدة
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            ImportThesesSeeder::class,
            ImportReservedThesisTitlesSeeder::class,
            SpecializationsFromFoldersSeeder::class, // إضافة Seeder التخصصات الجديدة
            // ImportThesisTitlesSimpleSeeder::class, // تم إلغاء Seed لهذا الجدول
        ]);
    }
}
