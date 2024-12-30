<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Equipment;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('equipments')->get(); 
        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        $equipments = Equipment::all(); 
        return view('rooms.create', compact('equipments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'equipments' => 'array', 
            'equipments.*' => 'exists:equipments,id', 
        ]);

        $room = Room::create($request->only('name', 'description', 'capacity'));

        
        $room->equipments()->sync($request->equipments);

        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
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
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'equipments' => 'array', 
            'equipments.*' => 'exists:equipments,id', 
        ]);

        $room->update($request->only('name', 'description', 'capacity'));

        
        $room->equipments()->sync($request->equipments);

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        $room->equipments()->detach(); 
        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }
}
