<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function calendar($roomId)
    {
        $room = Room::findOrFail($roomId); // Vérifie si la salle existe
        return view('reservations.calendar', compact('room')); // Renvoie la salle et le calendrier
    }

    public function getReservationsByRoom($roomId)
    {
        $reservations = Reservation::where('room_id', $roomId)->get();

        return $reservations->map(function ($reservation) {
            return [
                'title' => 'Réservé par ' . ($reservation->user->name ?? 'Utilisateur'),
                'start' => $reservation->start_time,
                'end' => $reservation->end_time,
            ];
        });
    }

    public function getReservations()
    {
        $reservations = Reservation::with('room')->get();

        return $reservations->map(function ($reservation) {
            return [
                'title' => $reservation->room->name,
                'start' => $reservation->start_time,
                'end' => $reservation->end_time,
            ];
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        // Vérification des chevauchements
        $overlap = Reservation::where('room_id', $validated['room_id'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function ($query) use ($validated) {
                          $query->where('start_time', '<=', $validated['start_time'])
                                ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->exists();

        if ($overlap) {
            return response()->json(['error' => 'Le créneau est déjà réservé.'], 422);
        }

        $reservation = Reservation::create([
            'user_id' => auth()->id(), // Ajout de l'utilisateur authentifié
            'room_id' => $validated['room_id'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        return response()->json([
            'success' => 'Réservation créée avec succès.',
            'reservation' => [
                'title' => 'Réservé par ' . ($reservation->user->name ?? 'Utilisateur'),
                'start' => $reservation->start_time,
                'end' => $reservation->end_time,
            ],
        ]);
    }
}
