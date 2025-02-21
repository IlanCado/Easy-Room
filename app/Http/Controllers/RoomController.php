<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Class RoomController
 * Gère la gestion des salles : affichage, création, modification et suppression.
 *
 * @package App\Http\Controllers
 */
class RoomController extends Controller
{
    /**
     * Affiche la liste des salles avec filtres par capacité et équipements.
     *
     * @param \Illuminate\Http\Request $request Requête contenant les paramètres de filtrage
     * @return \Illuminate\View\View Vue affichant la liste des salles
     */
    public function index(Request $request)
    {
        $capacity = $request->query('capacity');
        $equipmentIds = $request->query('equipments', []);

        // Construction dynamique de la requête
        $query = Room::query();

        if ($capacity) {
            $query->where('capacity', '>=', $capacity);
        }

        if (!empty($equipmentIds)) {
            $query->whereHas('equipments', function ($q) use ($equipmentIds) {
                $q->whereIn('equipments.id', $equipmentIds);
            });
        }

        $rooms = $query->with('equipments')->get();
        $equipments = Equipment::all(); // Liste complète des équipements pour les filtres

        return view('rooms.index', compact('rooms', 'equipments', 'capacity', 'equipmentIds'));
    }

    /**
     * Affiche le formulaire de création d'une salle.
     *
     * @return \Illuminate\View\View Vue du formulaire de création
     */
    public function create()
    {
        $equipments = Equipment::all();
        return view('rooms.create', compact('equipments'));
    }

    /**
     * Enregistre une nouvelle salle avec ses équipements associés.
     *
     * @param \Illuminate\Http\Request $request Requête contenant les données de la salle
     * @return \Illuminate\Http\RedirectResponse Redirection avec un message de succès ou d'erreur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'equipments' => 'nullable|array',
            'equipments.*' => 'exists:equipments,id',
        ]);

        // Gestion de l'upload d'image
        if ($request->hasFile('image')) {
            $fileName = time() . '_' . $request->file('image')->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('rooms', $fileName, 'public');
            $validated['image'] = '/storage/' . $filePath;
        }

        $room = Room::create($validated);

        if ($request->has('equipments')) {
            $room->equipments()->sync($request->equipments);
        }

        return redirect()->route('home')->with('success', 'Salle créée avec succès.');
    }

    /**
     * Affiche les détails d'une salle.
     *
     * @param \App\Models\Room $room La salle à afficher
     * @return \Illuminate\View\View Vue affichant les détails de la salle
     */
    public function show(Room $room)
    {
        $room->load('equipments');
        return view('rooms.show', compact('room'));
    }

    /**
     * Affiche le formulaire d'édition d'une salle.
     *
     * @param \App\Models\Room $room La salle à modifier
     * @return \Illuminate\View\View Vue affichant le formulaire de modification
     */
    public function edit(Room $room)
    {
        $equipments = Equipment::all();
        $room->load('equipments');
        return view('rooms.edit', compact('room', 'equipments'));
    }

    /**
     * Met à jour une salle et ses équipements.
     *
     * @param \Illuminate\Http\Request $request Requête contenant les nouvelles informations de la salle
     * @param \App\Models\Room $room La salle à mettre à jour
     * @return \Illuminate\Http\RedirectResponse Redirection avec un message de succès
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'equipments' => 'nullable|array',
            'equipments.*' => 'exists:equipments,id',
        ]);

        // Gestion de l'upload d'une nouvelle image
        if ($request->hasFile('image')) {
            if ($room->image && Storage::exists('public/' . $room->image)) {
                Storage::delete('public/' . $room->image);
            }

            $fileName = time() . '_' . $request->file('image')->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('rooms', $fileName, 'public');
            $validated['image'] = '/storage/' . $filePath;
        }

        $room->update($validated);

        if ($request->has('equipments')) {
            $room->equipments()->sync($request->equipments);
        }

        return redirect()->route('home')->with('success', 'Salle mise à jour avec succès.');
    }

    /**
     * Supprime une salle ainsi que son image et ses relations avec les équipements.
     *
     * @param \App\Models\Room $room La salle à supprimer
     * @return \Illuminate\Http\RedirectResponse Redirection avec un message de confirmation
     */
    public function destroy(Room $room)
    {
        if ($room->image && Storage::exists('public/' . $room->image)) {
            Storage::delete('public/' . $room->image);
        }

        $room->equipments()->detach();
        $room->delete();

        return redirect()->route('home')->with('success', 'Salle supprimée avec succès.');
    }

    /**
     * Affiche la liste des salles pour l'administration.
     *
     * @return \Illuminate\View\View Vue affichant les salles avec leurs équipements
     */
    public function adminIndex()
    {
        $rooms = Room::with('equipments')->get();
        return view('admin.rooms', compact('rooms'));
    }
}
