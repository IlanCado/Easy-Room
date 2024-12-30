<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    /** @use HasFactory<\Database\Factories\EquipmentFactory> */
    use HasFactory;
    protected $table = 'equipments';
    protected $fillable = [
        'name',
    ];

    // Relation : Un équipement peut appartenir à plusieurs salles
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_equipment');
    }
}
