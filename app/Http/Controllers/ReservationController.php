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
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        $start_time = Carbon::parse($validated['start_time']);
        $end_time = Carbon::parse($validated['end_time']);
        $now = Carbon::now();
        $maxYear = $now->year + 2; // Restriction sur l'année n+2

        // Vérifie si la réservation commence dans le passé
        if ($start_time->isBefore($now)) {
            return redirect()->back()->with('error', 'Erreur : Vous ne pouvez pas réserver un créneau qui commence dans le passé.');
        }

        // Vérifie si l'heure de fin est avant l'heure de début
        if ($end_time->isBefore($start_time)) {
            return redirect()->back()->with('error', 'Erreur : L\'heure de fin ne peut pas être avant l\'heure de début.');
        }

        // Vérifie si la durée est inférieure à 30 minutes
        if ($start_time->diffInMinutes($end_time) < 30) {
            return redirect()->back()->with('error', 'Erreur : La durée minimale d\'une réservation est de 30 minutes.');
        }

        // Vérifie si la réservation commence et se termine entre 7h et 20h
        if ($start_time->hour < 7 || $end_time->hour > 20 || ($end_time->hour === 20 && $end_time->minute > 0)) {
            return redirect()->back()->with(
                'error',
                'Erreur : Les réservations sont autorisées uniquement entre 7h et 20h.'
            );
        }

        // Vérifie si la réservation s'étend sur plusieurs jours
        if ($start_time->toDateString() !== $end_time->toDateString()) {
            return redirect()->back()->with(
                'error',
                'Erreur : Les réservations ne peuvent pas s\'étendre sur plusieurs jours. Vous pouvez effectuer plusieurs réservations pour des jours différents.'
            );
        }

        // Vérifie si la réservation est au-delà de l'année n+2
        if ($start_time->year > $maxYear || $end_time->year > $maxYear) {
            return redirect()->back()->with(
                'error',
                'Erreur : Vous ne pouvez pas réserver de créneaux au-delà de l\'année ' . $maxYear . '.'
            );
        }

        // Vérification des chevauchements
        $overlap = Reservation::where('room_id', $validated['room_id'])
            ->where(function ($query) use ($start_time, $end_time) {
                $query->where(function ($query) use ($start_time, $end_time) {
                    $query->whereBetween('start_time', [$start_time, $end_time])
                          ->orWhereBetween('end_time', [$start_time, $end_time]);
                })
                ->orWhere(function ($query) use ($start_time, $end_time) {
                    $query->where('start_time', '<', $end_time)
                          ->where('end_time', '>', $start_time);
                });
            })
            ->where(function ($query) use ($start_time, $end_time) {
                // Exclut les cas où une réservation se termine exactement à l'heure de début ou commence à l'heure de fin
                $query->where('end_time', '<>', $start_time)
                      ->where('start_time', '<>', $end_time);
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()->with('error', 'Erreur : Le créneau est déjà réservé.');
        }

        try {
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
