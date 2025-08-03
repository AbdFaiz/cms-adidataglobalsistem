<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permissions
        Permission::firstOrCreate(['name' => 'create account', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'reset password', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create account for roles below', 'guard_name' => 'web']);

        // Buat roles
        $supervisor = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
        $leader = Role::firstOrCreate(['name' => 'Leader', 'guard_name' => 'web']);
        $adminTracking = Role::firstOrCreate(['name' => 'Admin Tracking', 'guard_name' => 'web']);
        $adminOfficer = Role::firstOrCreate(['name' => 'Admin Officer', 'guard_name' => 'web']);
        $customerService = Role::firstOrCreate(['name' => 'Customer Service', 'guard_name' => 'web']);

        // Assign permission ke role
        $supervisor->givePermissionTo(['create account', 'reset password', 'create account for roles below']);
        $leader->givePermissionTo(['create account', 'reset password', 'create account for roles below']);
        $adminTracking->givePermissionTo('reset password');
        $adminOfficer->givePermissionTo('reset password');
        $customerService->givePermissionTo('reset password');

        // Buat user dan assign role
        $users = [
            [
                'name' => 'abdul',
                'username' => 'abdul',
                'email' => 'abdul@gmail.com',
                'password' => 'abdul',
                'role' => 'Supervisor'
            ],
            [
                'name' => 'rahman',
                'username' => 'rahman',
                'email' => 'rahman@gmail.com',
                'password' => 'rahman',
                'role' => 'Leader'
            ],
            [
                'name' => 'andi',
                'username' => 'andi',
                'email' => 'andi@gmail.com',
                'password' => 'andi',
                'role' => 'Admin Tracking'
            ],
            [
                'name' => 'budi',
                'username' => 'budi',
                'email' => 'budi@gmail.com',
                'password' => 'budi',
                'role' => 'Admin Officer'
            ],
            [
                'name' => 'citra',
                'username' => 'citra',
                'email' => 'citra@gmail.com',
                'password' => 'citra',
                'role' => 'Customer Service'
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'email_verified_at' => now(),
                    'password' => Hash::make($data['password']),
                    'status' => 'active',
                ]
            );
            $user->assignRole($data['role']);
        }
    }
}
