<?php 

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReservationBB3Test extends TestCase
{
    use RefreshDatabase;

    // Vérifie qu'un utilisateur peut créer une réservation valide
    public function test_user_can_create_reservation()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $response = $this->actingAs($user)->post('/reservations', [
            'room_id' => $room->id,
            'start_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_time' => now()->addDays(1)->addHour()->format('Y-m-d H:i:s'),
        ]);

        // Vérifie que la réservation a bien été enregistrée en BDD
        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Votre réservation a été enregistrée.');
    }

    // Vérifie qu'un utilisateur ne peut pas créer une réservation avec une date dans le passé
    public function test_cannot_create_reservation_in_the_past()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $response = $this->actingAs($user)->post('/reservations', [
            'room_id' => $room->id,
            'start_time' => now()->subDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->subDay()->addHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Erreur : Vous ne pouvez pas réserver un créneau passé.');
        $this->assertDatabaseCount('reservations', 0);
    }

    // Vérifie qu'un utilisateur peut annuler sa réservation
    public function test_user_can_cancel_reservation()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);

        $response = $this->actingAs($user)->delete('/reservations/' . $reservation->id);

        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
        $response->assertRedirect(route('my-reservations'));
        $response->assertSessionHas('success', 'Réservation supprimée avec succès.');
    }

    // Vérifie qu'un utilisateur ne peut pas annuler la réservation d'un autre utilisateur
    public function test_user_cannot_cancel_someone_elses_reservation()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $room = Room::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user1->id,
            'room_id' => $room->id,
        ]);

        $response = $this->actingAs($user2)->delete('/reservations/' . $reservation->id);

        $response->assertForbidden();
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
    }

    // Vérifie qu'un utilisateur peut modifier sa réservation
    public function test_user_can_update_reservation()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);

        $newStartTime = now()->addDays(2)->format('Y-m-d H:i:s');
        $newEndTime = now()->addDays(2)->addHour()->format('Y-m-d H:i:s');

        $response = $this->actingAs($user)->put("/reservations/{$reservation->id}", [
            'start_time' => $newStartTime,
            'end_time' => $newEndTime,
        ]);

        $response->assertRedirect(route('my-reservations'));
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'start_time' => $newStartTime,
            'end_time' => $newEndTime,
        ]);
    }
}
