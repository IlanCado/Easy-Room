<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Reservation
 * Représente une réservation effectuée par un utilisateur pour une salle donnée.
 *
 * @package App\Models
 *
 * @property int $id Identifiant unique de la réservation
 * @property int $user_id Identifiant de l'utilisateur ayant effectué la réservation
 * @property int $room_id Identifiant de la salle réservée
 * @property \Illuminate\Support\Carbon $start_time Heure de début de la réservation
 * @property \Illuminate\Support\Carbon $end_time Heure de fin de la réservation
 * @property \Illuminate\Support\Carbon|null $created_at Date de création de la réservation
 * @property \Illuminate\Support\Carbon|null $updated_at Date de mise à jour de la réservation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation query()
 */
class Reservation extends Model
{
    use HasFactory;

    /**
     * Attributs qui peuvent être assignés massivement.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'room_id', 'start_time', 'end_time'];

    /**
     * Relation : Une réservation appartient à une seule salle.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
