<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Room
 * Représente une salle pouvant être réservée par les utilisateurs.
 *
 * @package App\Models
 *
 * @property int $id Identifiant unique de la salle
 * @property string $name Nom de la salle
 * @property string|null $description Description de la salle
 * @property int $capacity Capacité maximale de la salle
 * @property string|null $image URL de l'image associée à la salle
 * @property \Illuminate\Support\Carbon|null $created_at Date de création de la salle
 * @property \Illuminate\Support\Carbon|null $updated_at Date de mise à jour de la salle
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Room query()
 */
class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;

    /**
     * Attributs pouvant être assignés massivement.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'capacity',
        'image',
    ];

    /**
     * Relation : Une salle peut avoir plusieurs équipements.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function equipments()
    {
        return $this->belongsToMany(Equipment::class, 'room_equipment');
    }

    /**
     * Relation : Une salle peut avoir plusieurs réservations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
