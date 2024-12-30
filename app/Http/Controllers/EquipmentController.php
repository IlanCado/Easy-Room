<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::all();
        return view('equipments.index', compact('equipments'));
    }

    public function create()
    {
        return view('equipments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Equipment::create($request->all());

        return redirect()->route('equipments.index')->with('success', 'Equipment created successfully.');
    }

    public function edit(Equipment $equipment)
    {
        return view('equipments.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $equipment->update($request->all());

        return redirect()->route('equipments.index')->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        return redirect()->route('equipments.index')->with('success', 'Equipment deleted successfully.');
    }
}
