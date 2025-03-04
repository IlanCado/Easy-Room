<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory pour la génération de fausses données d'utilisateurs.
 *
 * Cette factory permet de créer des utilisateurs aléatoires, un administrateur,
 * ou un utilisateur de test avec des informations prédéfinies.
 *
 * @package Database\Factories
 */
class UserFactory extends Factory
{
    /**
     * Mot de passe actuel utilisé par la factory pour tous les utilisateurs.
     *
     * @var string|null
     */
    protected static ?string $password;

    /**
     * Définit l'état par défaut des utilisateurs générés.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'user',  // Par défaut, utilisateur standard
        ];
    }

    /**
     * Indique que l'utilisateur doit être non vérifié.
     *
     * @return static
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * État pour créer un administrateur.
     *
     * @return static
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Administrateur',
            'email' => 'admin@admin.com',
            'password' => Hash::make('adminadmin'),  
            'role' => 'admin',
        ]);
    }

    /**
     * État pour créer un utilisateur de test spécifique.
     *
     * @return static
     */
    public function testUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Utilisateur Test',
            'email' => 'user@user.com',
            'password' => Hash::make('useruser'),  
            'role' => 'user',
        ]);
    }
}
