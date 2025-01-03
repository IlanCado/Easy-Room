<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
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
                $q->whereIn('equipments.id', $equipmentIds); // Utilisation de `equipments.id` pour éviter l'ambiguïté
            });
        }

        $rooms = $query->with('equipments')->get();
        $equipments = Equipment::all(); // Liste complète des équipements pour les filtres

        return view('rooms.index', compact('rooms', 'equipments', 'capacity', 'equipmentIds'));
    }

    public function create()
    {
        $equipments = Equipment::all();
        return view('rooms.create', compact('equipments'));
    }

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

    public function show(Room $room)
    {
        $room->load('equipments');
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $equipments = Equipment::all();
        $room->load('equipments');
        return view('rooms.edit', compact('room', 'equipments'));
    }

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

    public function destroy(Room $room)
    {
        if ($room->image && Storage::exists('public/' . $room->image)) {
            Storage::delete('public/' . $room->image);
        }

        $room->equipments()->detach();
        $room->delete();

        return redirect()->route('home')->with('success', 'Salle supprimée avec succès.');
    }

    public function adminIndex()
    {
        $rooms = Room::with('equipments')->get();
        return view('admin.rooms', compact('rooms'));
    }
}
