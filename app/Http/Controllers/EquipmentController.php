<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

/**
 * Class EquipmentController
 * Gère la gestion des équipements : création, mise à jour, suppression et affichage.
 *
 * @package App\Http\Controllers
 */
class EquipmentController extends Controller
{
    /**
     * Affiche la liste des équipements.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $equipments = Equipment::all();
        return view('equipments.index', compact('equipments'));
    }

    /**
     * Affiche le formulaire de création d'un nouvel équipement.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('equipments.create');
    }

    /**
     * Enregistre un nouvel équipement en base de données.
     *
     * @param \Illuminate\Http\Request $request Requête contenant les données de l'équipement
     * @return \Illuminate\Http\RedirectResponse Redirection vers la liste des équipements avec un message de succès
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Equipment::create([
            'name' => $request->name,
        ]);

        return redirect()->route('equipments.index')->with('success', 'Équipement créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un équipement.
     *
     * @param \App\Models\Equipment $equipment L'équipement à modifier
     * @return \Illuminate\View\View
     */
    public function edit(Equipment $equipment)
    {
        return view('equipments.edit', compact('equipment'));
    }

    /**
     * Met à jour un équipement existant.
     *
     * @param \Illuminate\Http\Request $request Requête contenant les nouvelles données
     * @param \App\Models\Equipment $equipment L'équipement à mettre à jour
     * @return \Illuminate\Http\RedirectResponse Redirection vers la liste des équipements avec un message de succès
     */
    public function update(Request $request, Equipment $equipment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $equipment->update([
            'name' => $request->name,
        ]);

        return redirect()->route('equipments.index')->with('success', 'Équipement mis à jour avec succès.');
    }

    /**
     * Supprime un équipement de la base de données.
     *
     * @param \App\Models\Equipment $equipment L'équipement à supprimer
     * @return \Illuminate\Http\RedirectResponse Redirection vers la liste des équipements avec un message de succès
     */
    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        return redirect()->route('equipments.index')->with('success', 'Équipement supprimé avec succès.');
    }
}
