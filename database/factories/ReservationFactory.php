<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id, // Crée un utilisateur et récupère son ID
            'room_id' => Room::factory()->create()->id, // Crée une salle et récupère son ID
           'start_time' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'), // Correct format MySQL
            'end_time' => Carbon::now()->addDays(1)->addHours(1)->format('Y-m-d H:i:s'),

        ];
    }

    public function test_user_can_view_their_reservations()
{
    $user = User::factory()->create();
    $myReservation = Reservation::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get('/my-reservations');

    dd($response->content()); // <== Ajoute cette ligne pour voir la page HTML renvoyée
}

}
