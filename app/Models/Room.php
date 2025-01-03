<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'capacity',
        'image',
    ];

    // Relation : Une salle peut avoir plusieurs équipements
    public function equipments()
    {
        return $this->belongsToMany(Equipment::class, 'room_equipment');
    }

    // Relation : Une salle peut avoir plusieurs réservations
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
