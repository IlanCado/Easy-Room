<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function calendar($roomId)
    {
        // Vérifie si la salle existe
        $room = Room::findOrFail($roomId);
        return view('reservations.calendar', compact('room'));
    }

    public function getReservationsByRoom($roomId)
    {
        $reservations = Reservation::where('room_id', $roomId)->get();

        // Formate les données pour FullCalendar
        return $reservations->map(function ($reservation) {
            return [
                'title' => 'Réservé par ' . ($reservation->user->name ?? 'Utilisateur'),
                'start' => $reservation->start_time,
                'end' => $reservation->end_time,
            ];
        });
    }

    public function store(Request $request)
    {
        // Valide les données de la requête
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        try {
            // Conversion des dates en format compatible avec la base de données
            $start_time = Carbon::parse($validated['start_time'])->format('Y-m-d H:i:s');
            $end_time = Carbon::parse($validated['end_time'])->format('Y-m-d H:i:s');

            // Vérification des chevauchements
            $overlap = Reservation::where('room_id', $validated['room_id'])
                ->where(function ($query) use ($start_time, $end_time) {
                    $query->whereBetween('start_time', [$start_time, $end_time])
                        ->orWhereBetween('end_time', [$start_time, $end_time])
                        ->orWhere(function ($query) use ($start_time, $end_time) {
                            $query->where('start_time', '<=', $start_time)
                                ->where('end_time', '>=', $end_time);
                        });
                })
                ->exists();

            if ($overlap) {
                return response()->json(['error' => 'Le créneau est déjà réservé.'], 422);
            }

            // Création de la réservation
            $reservation = Reservation::create([
                'user_id' => auth()->id(),
                'room_id' => $validated['room_id'],
                'start_time' => $start_time,
                'end_time' => $end_time,
            ]);

            return response()->json([
                'success' => 'Réservation créée avec succès.',
                'reservation' => [
                    'title' => 'Réservé par ' . (auth()->user()->name ?? 'Utilisateur'),
                    'start' => $reservation->start_time,
                    'end' => $reservation->end_time,
                ],
            ]);
        } catch (\Exception $e) {
            // Gère les erreurs
            return response()->json(['error' => 'Erreur lors de la création de la réservation.'], 500);
        }
    }
}
