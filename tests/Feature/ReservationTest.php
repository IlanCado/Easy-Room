<?php 

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    // Vérifie qu'un utilisateur peut voir ses propres réservations
    public function test_user_can_view_their_reservations()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
    
        $myReservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_time' => now()->addDays(1),
            'end_time' => now()->addDays(1)->addHour(),
        ]);
    
        $response = $this->actingAs($user)->get('/my-reservations');
    
        $response->assertStatus(200); // Vérifie que la requête réussit
        $response->assertSee($myReservation->room->name); // Vérifie que le nom de la salle est affiché
        $response->assertSee($myReservation->start_time->format('d/m/Y H:i')); // Vérifie l'affichage de la date de début
        $response->assertSee($myReservation->end_time->format('d/m/Y H:i')); // Vérifie l'affichage de la date de fin
    }
    
    // Vérifie qu'un utilisateur peut annuler sa propre réservation
    public function test_user_can_cancel_reservation()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $myReservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);

        $response = $this->actingAs($user)->delete('/reservations/' . $myReservation->id);

        $this->assertDatabaseMissing('reservations', ['id' => $myReservation->id]); // Vérifie que la réservation est bien supprimée
        $response->assertRedirect(route('my-reservations')); // Vérifie la redirection après suppression
        $response->assertSessionHas('success', 'Réservation supprimée avec succès.'); // Vérifie le message de succès
    }

    // Vérifie qu'on ne peut pas créer une réservation dans le passé
    public function test_cannot_create_reservation_in_the_past()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $response = $this->actingAs($user)->post('/reservations', [
            'room_id' => $room->id,
            'start_time' => now()->subDays(1)->format('Y-m-d H:i:s'), // Date passée
            'end_time' => now()->subDays(1)->addHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(); // Vérifie que l'utilisateur est redirigé
        $response->assertSessionHas('error', 'Erreur : Vous ne pouvez pas réserver un créneau passé.'); // Vérifie le message d'erreur
        $this->assertEquals(0, Reservation::count()); // Vérifie qu'aucune réservation n'a été créée
    }

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

        // Vérifie que la réservation est bien enregistrée dans la base de données
        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_time' => now()->addDays(1)->addHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(); // Vérifie que la requête redirige bien l'utilisateur
        $response->assertSessionHas('success', 'Votre réservation a été enregistrée.'); // Vérifie le message de confirmation
    }

    // Vérifie qu'un utilisateur ne peut pas annuler la réservation d'un autre utilisateur
    public function test_user_cannot_cancel_someone_elses_reservation()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create(); // Un autre utilisateur
        $room = Room::factory()->create();

        $reservation = Reservation::factory()->create([
            'user_id' => $user1->id,
            'room_id' => $room->id,
        ]);

        $response = $this->actingAs($user2)->delete('/reservations/' . $reservation->id);

        $response->assertForbidden(); // Vérifie que la requête est interdite (403)
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]); // Vérifie que la réservation existe toujours
    }
}
