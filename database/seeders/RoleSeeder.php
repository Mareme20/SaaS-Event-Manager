<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création des rôles
        $adminRole = Role::create(['name' => 'administrateur']);
        $organizerRole = Role::create(['name' => 'organisateur']);
        $photographerRole = Role::create(['name' => 'photographe']);
        $guestRole = Role::create(['name' => 'invité']);

        // Création d'un utilisateur admin de test
        $admin = User::create([
            'name' => 'Admin SaaS',
            'email' => 'admin@saas-event.com',
            'password' => Hash::make('password'),
        ]);

        $admin->assignRole($adminRole);

        echo "Rôles créés et utilisateur admin@saas-event.com généré.\n";
    }
}
