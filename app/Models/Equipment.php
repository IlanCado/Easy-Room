<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Equipment
 * Représente un équipement pouvant être utilisé dans plusieurs salles.
 *
 * @package App\Models
 *
 * @property int $id Identifiant unique de l'équipement
 * @property string $name Nom de l'équipement
 * @property \Illuminate\Support\Carbon|null $created_at Date de création
 * @property \Illuminate\Support\Carbon|null $updated_at Date de mise à jour
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Equipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Equipment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Equipment query()
 */
class Equipment extends Model
{
    /** @use HasFactory<\Database\Factories\EquipmentFactory> */
    use HasFactory;

    /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'equipments';

    /**
     * Attributs qui peuvent être assignés massivement.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Relation : Un équipement peut appartenir à plusieurs salles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_equipment');
    }
}
