<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'room_id',
        'start_time',
        'end_time',
    ];

    // Relation : Une réservation appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation : Une réservation concerne une salle
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
