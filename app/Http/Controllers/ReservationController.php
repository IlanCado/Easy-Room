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
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        try {
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
                return redirect()->back()->with('error', 'Le créneau est déjà réservé.');
            }

            Reservation::create([
                'user_id' => auth()->id(),
                'room_id' => $validated['room_id'],
                'start_time' => $start_time,
                'end_time' => $end_time,
            ]);

            return redirect()->back()->with('success', 'Votre réservation a été prise en compte.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la création de la réservation.');
        }
    }

    public function userReservations()
    {
        $reservations = Reservation::with('room')
            ->where('user_id', auth()->id())
            ->get();

        return view('reservations.user-reservations', compact('reservations'));
    }

    public function show($id)
    {
        $reservation = Reservation::with('room')->findOrFail($id);

        if ($reservation->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette réservation.');
        }

        return view('reservations.details', compact('reservation'));
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette réservation.');
        }

        try {
            $reservation->delete();
            return redirect()->route('my-reservations')->with('success', 'Réservation supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('my-reservations')->with('error', 'Erreur lors de la suppression de la réservation.');
        }
    }
}
