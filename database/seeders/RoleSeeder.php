<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Menu;
use App\Models\RoleMenuAccess;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles
        $roles = [
            [
                'code' => 'SUPERADMIN',
                'name' => 'Super Administrator',
                'description' => 'Full access to all features and menus',
                'is_active' => true,
            ],
            [
                'code' => 'ADMIN',
                'name' => 'Administrator',
                'description' => 'Access to admin features',
                'is_active' => true,
            ],
            [
                'code' => 'KASIR',
                'name' => 'Kasir',
                'description' => 'Access to cashier/transaction features',
                'is_active' => true,
            ],
            [
                'code' => 'STAFF',
                'name' => 'Staff',
                'description' => 'Limited access to basic features',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['code' => $role['code']],
                $role
            );
        }

        // Grant SUPERADMIN access to all menus
        $superAdminRole = Role::where('code', 'SUPERADMIN')->first();
        if ($superAdminRole) {
            $menus = Menu::all();
            foreach ($menus as $menu) {
                RoleMenuAccess::updateOrCreate(
                    [
                        'role_code' => 'SUPERADMIN',
                        'menu_id' => $menu->id,
                    ],
                    [
                        'can_view' => 1,
                        'can_create' => 1,
                        'can_edit' => 1,
                        'can_delete' => 1,
                    ]
                );
            }
        }

        // Grant ADMIN access to most menus except role management
        $adminRole = Role::where('code', 'ADMIN')->first();
        if ($adminRole) {
            $menus = Menu::all();
            foreach ($menus as $menu) {
                // Exclude role and role-menu-access from admin
                $excludedUrls = ['role', 'role-menu-access'];
                $canAccess = !in_array($menu->url, $excludedUrls) ? 1 : 0;

                RoleMenuAccess::updateOrCreate(
                    [
                        'role_code' => 'ADMIN',
                        'menu_id' => $menu->id,
                    ],
                    [
                        'can_view' => $canAccess,
                        'can_create' => $canAccess,
                        'can_edit' => $canAccess,
                        'can_delete' => $canAccess,
                    ]
                );
            }
        }

        // Grant KASIR access to transaction-related menus
        $kasirRole = Role::where('code', 'KASIR')->first();
        if ($kasirRole) {
            $allowedUrls = ['dashboard', 'transaction', 'customer', 'item'];
            $menus = Menu::all();

            foreach ($menus as $menu) {
                $canView = (in_array($menu->url, $allowedUrls) || $menu->is_header === 'true') ? 1 : 0;

                RoleMenuAccess::updateOrCreate(
                    [
                        'role_code' => 'KASIR',
                        'menu_id' => $menu->id,
                    ],
                    [
                        'can_view' => $canView,
                        'can_create' => in_array($menu->url, ['transaction', 'customer']) ? 1 : 0,
                        'can_edit' => in_array($menu->url, ['transaction']) ? 1 : 0,
                        'can_delete' => 0,
                    ]
                );
            }
        }

        $this->command->info('Roles and menu access seeded successfully!');
    }
}
