<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Members
            'view members', 'create members', 'edit members', 'delete members',
            // Clubs
            'view clubs', 'create clubs', 'edit clubs', 'delete clubs',
            // Districts
            'view districts', 'create districts', 'edit districts', 'delete districts',
            // Events
            'view events', 'create events', 'edit events', 'delete events', 'export events',
            // Announcements
            'view announcements', 'create announcements', 'edit announcements', 'delete announcements',
            // Documents
            'view documents', 'upload documents', 'delete documents',
            // Applications
            'view applications', 'approve applications',
            // Reports
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'super_admin' => Permission::all()->pluck('name')->toArray(),
            'hq_admin' => $permissions,
            'district_admin' => [
                'view members', 'create members', 'edit members',
                'view clubs', 'edit clubs',
                'view districts', 'edit districts',
                'view events', 'create events', 'edit events', 'export events',
                'view announcements', 'create announcements', 'edit announcements',
                'view documents', 'upload documents',
                'view applications', 'approve applications',
                'view reports',
            ],
            'club_admin' => [
                'view members', 'create members', 'edit members',
                'view clubs', 'edit clubs',
                'view events', 'create events', 'edit events', 'export events',
                'view announcements', 'create announcements', 'edit announcements',
                'view documents', 'upload documents',
                'view reports',
            ],
            'club_officer' => [
                'view members',
                'view clubs',
                'view events', 'create events', 'edit events',
                'view announcements', 'create announcements',
                'view documents', 'upload documents',
            ],
            'member' => [
                'view members', 'view clubs', 'view districts',
                'view events', 'view announcements', 'view documents',
            ],
            'guest' => [],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
