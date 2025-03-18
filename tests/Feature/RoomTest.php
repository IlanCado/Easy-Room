<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste la création d'une salle valide.
     */
    public function test_creer_salle_valide()
    {
        $admin = User::factory()->admin()->create(); // Crée un administrateur
        $this->actingAs($admin); // Simule la connexion

        $response = $this->post(route('rooms.store'), [
            'name' => 'Salle de conférence',
            'description' => 'Une salle équipée pour les réunions',
            'capacity' => 20,
            'image' => null,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('rooms', ['name' => 'Salle de conférence']);
    }

    /**
     * Teste la création d'une salle sans nom (devrait échouer).
     */
    public function test_creer_salle_sans_nom()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $response = $this->post(route('rooms.store'), [
            'description' => 'Une grande salle',
            'capacity' => 50,
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseCount('rooms', 0);
    }

    /**
     * Teste la création d'une salle avec une capacité invalide (devrait échouer).
     */
    public function test_creer_salle_avec_capacite_invalide()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $response = $this->post(route('rooms.store'), [
            'name' => 'Salle VIP',
            'capacity' => 'invalide',
        ]);

        $response->assertSessionHasErrors(['capacity']);
        $this->assertDatabaseCount('rooms', 0);
    }

    /**
     * Teste la mise à jour d'une salle avec des données valides.
     */
    public function test_mettre_a_jour_salle_valide()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $room = Room::factory()->create(['name' => 'Ancienne Salle', 'capacity' => 15]);

        $response = $this->put(route('rooms.update', $room->id), [
            'name' => 'Salle de formation',
            'capacity' => 30,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'name' => 'Salle de formation']);
    }

    /**
     * Teste la suppression d'une salle.
     */
    public function test_supprimer_salle()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $room = Room::factory()->create();

        $response = $this->delete(route('rooms.destroy', $room->id));

        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }

    /**
 * Teste que la suppression d'une salle avec des réservations est impossible.
 */
public function test_cannot_delete_room_with_reservations()
{
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    // Crée une salle
    $room = Room::factory()->create();

    // Associe une réservation à cette salle
    \App\Models\Reservation::factory()->create(['room_id' => $room->id]);

    // Tente de supprimer la salle
    $response = $this->delete(route('rooms.destroy', $room->id));

    // Vérifie que la salle est toujours en base de données
    $this->assertDatabaseHas('rooms', ['id' => $room->id]);

    // Vérifie qu'on reçoit bien un message d'erreur
    $response->assertRedirect(route('home'));
    $response->assertSessionHas('error', 'Impossible de supprimer une salle ayant des réservations actives.');
}

}
