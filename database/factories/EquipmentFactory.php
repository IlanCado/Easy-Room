<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory pour la génération de fausses données de la table `equipments`.
 *
 * Cette factory permet de créer des équipements avec des noms réalistes
 * pour peupler la base de données lors des tests ou du développement.
 *
 * @package Database\Factories
 */
class EquipmentFactory extends Factory
{
    /**
     * Définition du modèle de données par défaut pour un équipement.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Projecteur', 
                'Tableau blanc', 
                'Connexion Wi-Fi', 
                'Climatisation', 
                'Système de visioconférence', 
                'Microphone',   
                'Prises électriques', 
                'Téléviseur écran plat'
            ]),
        ];
    }
}
