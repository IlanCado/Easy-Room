<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * Représente un utilisateur du système pouvant effectuer des réservations.
 *
 * @package App\Models
 *
 * @property int $id Identifiant unique de l'utilisateur
 * @property string $name Nom de l'utilisateur
 * @property string $email Adresse email de l'utilisateur
 * @property string $password Mot de passe hashé de l'utilisateur
 * @property string $role Rôle de l'utilisateur (ex: admin, user)
 * @property \Illuminate\Support\Carbon|null $email_verified_at Date de vérification de l'email
 * @property string|null $remember_token Jeton de connexion automatique
 * @property \Illuminate\Support\Carbon|null $created_at Date de création du compte utilisateur
 * @property \Illuminate\Support\Carbon|null $updated_at Date de mise à jour du compte utilisateur
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Attributs pouvant être assignés massivement.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Relation : Un utilisateur peut avoir plusieurs réservations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Attributs à cacher lors de la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Définir les types de données à caster automatiquement.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
