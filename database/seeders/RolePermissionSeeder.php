<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $writerTitlesRole = Role::where('name', 'writer-titles')->first();
        $writerThesesRole = Role::where('name', 'writer-theses')->first();

        $permissions = Permission::all();

        // super_admin has all permissions
        $superAdminRole->permissions()->sync($permissions->pluck('id'));

        // admin has all permissions except 'إضافة مستخدمين'
        $adminPermissions = $permissions->filter(function ($permission) {
            return $permission->name !== 'إضافة مستخدمين';
        })->pluck('id');
        $adminRole->permissions()->sync($adminPermissions);

        // writer-titles has 'العناوين محجوزة' permission
        $writerTitlesPermission = $permissions->where('name', 'العناوين محجوزة')->first();
        if ($writerTitlesPermission) {
            $writerTitlesRole->permissions()->sync($writerTitlesPermission->id);
        }

        // writer-theses has 'الرسائل' permission
        $writerThesesPermission = $permissions->where('name', 'الرسائل')->first();
        if ($writerThesesPermission) {
            $writerThesesRole->permissions()->sync($writerThesesPermission->id);
        }

        // thesis-and-titles-reader has 'الرسائل' and 'العناوين محجوزة' permissions
        $thesisAndTitlesReaderRole = Role::where('name', 'thesis-and-titles-reader')->first();
        $thesisPermission = $permissions->where('name', 'الرسائل')->first();
        $reservedTitlesPermission = $permissions->where('name', 'العناوين محجوزة')->first();

        if ($thesisAndTitlesReaderRole) {
            $permissionsToAttach = [];
            if ($thesisPermission) {
                $permissionsToAttach[] = $thesisPermission->id;
            }
            if ($reservedTitlesPermission) {
                $permissionsToAttach[] = $reservedTitlesPermission->id;
            }
            $thesisAndTitlesReaderRole->permissions()->sync($permissionsToAttach);
        }
    }
}
