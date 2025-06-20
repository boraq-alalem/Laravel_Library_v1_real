<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'إضافة مستخدمين'],
            ['name' => 'العناوين محجوزة'],
            ['name' => 'الرسائل'],
            ['name' => 'إضافة الجامعات والتعديل عليها'],
            ['name' => 'إضافة التخصصات والتعديل عليها'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']]);
        }
    }
}
