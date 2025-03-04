<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Factory pour la génération de fausses réservations respectant les règles métiers.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    /**
     * Génère une réservation valide respectant toutes les règles métiers.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $room = Room::inRandomOrder()->first() ?? Room::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        $start_time = $this->generateValidStartTime();
        $end_time = (clone $start_time)->addHours(2);

        while (!$this->isTimeSlotValid($room->id, $start_time, $end_time)) {
            $start_time = $this->generateValidStartTime();
            $end_time = (clone $start_time)->addHours(2);
        }

        return [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ];
    }

    /**
     * Génère un créneau horaire valide respectant les règles de base :
     * - Entre 7h et 18h (pour laisser la place à une réservation de 2h max).
     * - Toujours sur la même journée.
     * - Dans les 2 ans à venir.
     */
    private function generateValidStartTime(): Carbon
    {
        $now = Carbon::now();
        $maxDate = $now->copy()->addYears(2);

        return Carbon::today()
            ->addDays(rand(1, 60))
            ->setHour(rand(7, 17))
            ->setMinute(0)
            ->setSecond(0);
    }

    /**
     * Vérifie si un créneau est valide pour une salle donnée.
     * - Respecte la durée minimale (30 minutes).
     * - Pas de chevauchement avec une réservation existante.
     */
    private function isTimeSlotValid(int $roomId, Carbon $start_time, Carbon $end_time): bool
    {
        if ($start_time->isPast()) {
            return false;
        }

        if ($end_time->lessThan($start_time)) {
            return false;
        }

        if ($start_time->diffInMinutes($end_time) < 30) {
            return false;
        }

        if ($start_time->hour < 7 || $end_time->hour > 20 || ($end_time->hour === 20 && $end_time->minute > 0)) {
            return false;
        }

        if ($start_time->toDateString() !== $end_time->toDateString()) {
            return false;
        }

        $now = Carbon::now();
        $maxYear = $now->year + 2;
        if ($start_time->year > $maxYear || $end_time->year > $maxYear) {
            return false;
        }

        $overlap = Reservation::where('room_id', $roomId)
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time, $end_time])
                    ->orWhereBetween('end_time', [$start_time, $end_time])
                    ->orWhere(function ($query) use ($start_time, $end_time) {
                        $query->where('start_time', '<', $end_time)
                            ->where('end_time', '>', $start_time);
                    });
            })
            ->exists();

        return !$overlap;
    }
}
