<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Expense;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Création des Rôles
        $adminRole = Role::firstOrCreate(['name' => 'administrateur']);
        $organizerRole = Role::firstOrCreate(['name' => 'organisateur']);

        // Création de l'utilisateur Admin principal
        $admin = User::firstOrCreate(
            ['email' => 'admin@saas-event.com'],
            [
                'name' => 'Admin SaaS Premium',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // Événements types pour l'Afrique de l'Ouest
        $eventTypes = [
            ['title' => 'Mariage Royal à Dakar', 'budget' => 15000000, 'location' => 'Terrou-Bi'],
            ['title' => 'Conférence Tech Sahel', 'budget' => 5000000, 'location' => 'Grand Théâtre'],
            ['title' => 'Anniversaire VIP', 'budget' => 2000000, 'location' => 'Ngor Virage'],
            ['title' => 'Concert de Bienfaisance', 'budget' => 10000000, 'location' => 'Stade Léopold Sédar Senghor'],
            ['title' => 'Inauguration Galerie d\'Art', 'budget' => 3500000, 'location' => 'Plateau'],
        ];

        foreach ($eventTypes as $type) {
            $event = Event::create([
                'user_id' => $admin->id,
                'title' => $type['title'],
                'slug' => Str::slug($type['title']) . '-' . Str::random(4),
                'description' => 'Un événement prestigieux géré sur la plateforme SaaS Event Manager.',
                'date' => now()->addDays(rand(10, 60)),
                'location' => $type['location'],
                'budget_estimated' => $type['budget'],
                'status' => 'planned',
            ]);

            // Dépenses types pour chaque événement
            $categories = ['Logistique', 'Restauration', 'Communication', 'Sécurité', 'Sonorisation'];
            
            for ($i = 0; $i < 4; $i++) {
                Expense::create([
                    'event_id' => $event->id,
                    'title' => 'Dépense ' . $categories[$i],
                    'amount' => rand($type['budget'] * 0.05, $type['budget'] * 0.15),
                    'category' => $categories[$i],
                    'date' => now()->addDays(rand(1, 5)),
                ]);
            }
        }

        echo "Base de données réinitialisée avec des données premium !\n";
    }
}
