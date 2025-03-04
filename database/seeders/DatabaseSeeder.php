<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Exécute les seeders pour peupler la base de données.
     *
     * @return void
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clés étrangères pour éviter les erreurs de relation
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Nettoyer les tables pour éviter les doublons lors des tests
        User::truncate();
        Room::truncate();
        Equipment::truncate();
        Reservation::truncate();
        DB::table('room_equipment')->truncate();

        // Réactiver les contraintes
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Création de l'admin et du user de test
        User::factory()->admin()->create();
        User::factory()->testUser()->create();

        // Création de 10 utilisateurs standards
        User::factory(10)->create();

        // Création de 10 équipements réalistes
        Equipment::factory()->createMany([
            ['name' => 'Projecteur'],
            ['name' => 'Tableau Blanc'],
            ['name' => 'Système de visioconférence'],
            ['name' => 'Climatisation'],
            ['name' => 'Ordinateur'],
            ['name' => 'Télévision'],
            ['name' => 'Connexion Wi-Fi'],
            ['name' => 'Micro'],
            ['name' => 'Enceintes'],
            ['name' => 'Câbles HDMI'],
        ]);

        // Création de 10 salles
        $rooms = Room::factory(10)->create();

        // Associer aléatoirement des équipements aux salles
        $equipments = Equipment::all();

        foreach ($rooms as $room) {
            $room->equipments()->attach(
                $equipments->random(rand(3, 5))->pluck('id')->toArray()
            );
        }

        // Création de 20 réservations aléatoires (liées aux salles et utilisateurs existants)
        for ($i = 0; $i < 300; $i++) {
            Reservation::factory()->create();
        }
        
        $this->command->info('Base de données initialisée avec succès !');
    }
}
