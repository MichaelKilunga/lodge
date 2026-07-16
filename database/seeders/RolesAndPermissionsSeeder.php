<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Define all permissions
        $permissions = [
            // Dashboard
            ['key' => 'view_dashboard', 'name' => 'View Dashboard', 'group' => 'Dashboard'],
            
            // Transactions (Bookings / Payments)
            ['key' => 'view_transactions', 'name' => 'View Transactions', 'group' => 'Transactions'],
            ['key' => 'manage_reservations', 'name' => 'Manage Reservations', 'group' => 'Transactions'],
            ['key' => 'manage_payments', 'name' => 'Manage Payments', 'group' => 'Transactions'],

            // Room Management
            ['key' => 'view_rooms', 'name' => 'View Rooms', 'group' => 'Room Management'],
            ['key' => 'manage_rooms', 'name' => 'Manage Rooms', 'group' => 'Room Management'],
            ['key' => 'manage_room_types', 'name' => 'Manage Room Types', 'group' => 'Room Management'],
            ['key' => 'manage_room_statuses', 'name' => 'Manage Room Statuses', 'group' => 'Room Management'],
            ['key' => 'manage_facilities', 'name' => 'Manage Facilities', 'group' => 'Room Management'],

            // User Management
            ['key' => 'view_customers', 'name' => 'View Customers', 'group' => 'User Management'],
            ['key' => 'manage_customers', 'name' => 'Manage Customers', 'group' => 'User Management'],
            ['key' => 'manage_staff', 'name' => 'Manage Staff/Users', 'group' => 'User Management'],
            ['key' => 'manage_roles', 'name' => 'Manage Roles & Permissions', 'group' => 'User Management'],

            // Analytics & Content
            ['key' => 'view_reports', 'name' => 'View Reports', 'group' => 'Analytics'],
            ['key' => 'manage_blog', 'name' => 'Manage Blog Posts', 'group' => 'Content'],

            // Administration
            ['key' => 'manage_settings', 'name' => 'Manage Settings', 'group' => 'Administration'],
            ['key' => 'manage_payment_accounts', 'name' => 'Manage Payment Accounts', 'group' => 'Administration'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['key' => $perm['key']], $perm);
        }

        // 2. Create System Roles
        $superRole = Role::updateOrCreate(
            ['name' => 'Super'],
            ['description' => 'Super Administrator with full access', 'is_system' => true]
        );
        // Super bypasses all checks dynamically, but we can attach all just in case
        $superRole->permissions()->sync(Permission::pluck('id'));

        $adminRole = Role::updateOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Administrator with most access', 'is_system' => true]
        );
        // Admin gets everything EXCEPT roles/staff management (for example)
        $adminPermissions = Permission::whereNotIn('key', ['manage_roles', 'manage_staff'])->pluck('id');
        $adminRole->permissions()->sync($adminPermissions);

        $ownerRole = Role::updateOrCreate(
            ['name' => 'Owner'],
            ['description' => 'Business Owner with access to all business operations', 'is_system' => true]
        );
        // Owner gets all business permissions, excluding technical features (settings and roles)
        $ownerPermissions = Permission::whereNotIn('key', ['manage_roles', 'manage_settings'])->pluck('id');
        $ownerRole->permissions()->sync($ownerPermissions);


        $customerRole = Role::updateOrCreate(
            ['name' => 'Customer'],
            ['description' => 'Hotel Guest', 'is_system' => true]
        );
        // Customers don't need back-office permissions, they have their own limited views

        $frontDeskRole = Role::updateOrCreate(
            ['name' => 'Front Desk'],
            ['description' => 'Front desk receptionist', 'is_system' => false]
        );
        // Custom role example: Front desk can manage bookings/rooms/customers but not settings or reports
        $frontDeskPermissions = Permission::whereIn('key', [
            'view_dashboard', 'view_transactions', 'manage_reservations', 'manage_payments',
            'view_rooms', 'manage_room_statuses', 'view_customers', 'manage_customers'
        ])->pluck('id');
        $frontDeskRole->permissions()->sync($frontDeskPermissions);


        // 3. Migrate existing users to the new role_id based on their string `role` column
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role === 'Super') {
                $user->role_id = $superRole->id;
            } elseif ($user->role === 'Admin') {
                $user->role_id = $adminRole->id;
            } elseif ($user->role === 'Owner') {
                $user->role_id = $ownerRole->id;
            } elseif ($user->role === 'Customer') {
                $user->role_id = $customerRole->id;
            } elseif ($user->role === 'Front Desk') {
                $user->role_id = $frontDeskRole->id;
            } else {
                // Fallback to front desk or admin if somehow something else
                $user->role_id = $frontDeskRole->id;
            }

            $user->save();
        }

    }
}
