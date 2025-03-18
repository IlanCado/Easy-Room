<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste la création d'un équipement valide.
     */
    public function test_creer_equipement_valide()
    {
        $admin = User::factory()->admin()->create(); // Crée un administrateur
        $this->actingAs($admin); // Simule la connexion

        $response = $this->post(route('equipments.store'), [
            'name' => 'Projecteur',
        ]);

        // Vérifie que la création s'est bien faite et qu'on est redirigé
        $response->assertRedirect(route('equipments.index'));
        $this->assertDatabaseHas('equipments', ['name' => 'Projecteur']);
    }

    /**
     * Teste la création d'un équipement sans nom (devrait échouer).
     */
    public function test_creer_equipement_sans_nom()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $response = $this->post(route('equipments.store'), []);

        // Vérifie que la validation renvoie une erreur
        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseCount('equipments', 0); // Vérifie qu'aucun équipement n'a été créé
    }

    /**
     * Teste la mise à jour d'un équipement valide.
     */
    public function test_mettre_a_jour_equipement_valide()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $equipment = Equipment::factory()->create(['name' => 'Ancien Nom']);

        $response = $this->put(route('equipments.update', $equipment->id), [
            'name' => 'Tableau Blanc',
        ]);

        // Vérifie que la mise à jour est bien effectuée et que l'utilisateur est redirigé
        $response->assertRedirect(route('equipments.index'));
        $this->assertDatabaseHas('equipments', ['id' => $equipment->id, 'name' => 'Tableau Blanc']);
    }

    /**
     * Teste la mise à jour d'un équipement sans nom (devrait échouer).
     */
    public function test_mettre_a_jour_equipement_sans_nom()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $equipment = Equipment::factory()->create(['name' => 'Ancien Nom']);

        $response = $this->put(route('equipments.update', $equipment->id), [
            'name' => '',
        ]);

        // Vérifie que la validation renvoie une erreur et que le nom d'origine est toujours en BDD
        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseHas('equipments', ['id' => $equipment->id, 'name' => 'Ancien Nom']);
    }

    /**
     * Teste la suppression d'un équipement.
     */
    public function test_supprimer_equipement()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $equipment = Equipment::factory()->create();

        $response = $this->delete(route('equipments.destroy', $equipment->id));

        // Vérifie que la suppression a bien été effectuée
        $response->assertRedirect(route('equipments.index'));
        $this->assertDatabaseMissing('equipments', ['id' => $equipment->id]);
    }
}
