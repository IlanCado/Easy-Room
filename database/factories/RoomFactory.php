<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory pour la génération de fausses salles.
 *
 * Cette factory crée des salles avec des noms uniques combinant un type de salle,
 * un numéro aléatoire et une courte description.
 * L'image est par défaut à null.
 *
 * @package Database\Factories
 */
class RoomFactory extends Factory
{
    /**
     * Définit les valeurs par défaut pour une salle.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [
            'Salle de réunion',
            'Salle de conférence',
            'Espace collaboratif',
            'Bureau privé',
            'Auditorium',
            'Salle de formation',
            'Open space'
        ];

        $nomSalle = $this->faker->randomElement($types) . ' ' . $this->faker->unique()->numberBetween(1, 500);

        return [
            'name' => $nomSalle,
            'description' => $this->faker->sentence(),
            'capacity' => $this->faker->numberBetween(5, 100),
            'image' => null,  
        ];
    }
}
