<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Class ReservationController
 * Gère la réservation des salles : affichage du calendrier, création, affichage et suppression des réservations.
 *
 * @package App\Http\Controllers
 */
class ReservationController extends Controller
{
    /**
     * Affiche le calendrier des réservations pour une salle spécifique.
     *
     * @param int $roomId Identifiant de la salle
     * @return \Illuminate\View\View Vue du calendrier avec les réservations
     */
    public function calendar($roomId)
    {
        $room = Room::findOrFail($roomId);
        return view('reservations.calendar', compact('room'));
    }

    /**
     * Récupère les réservations d'une salle spécifique.
     *
     * @param int $roomId Identifiant de la salle
     * @return \Illuminate\Support\Collection Liste des réservations formatées pour FullCalendar.js
     */
    public function getReservationsByRoom($roomId)
    {
        $reservations = Reservation::where('room_id', $roomId)->get();

        return $reservations->map(function ($reservation) {
            return [
                'title' => 'Réservé',
                'start' => $reservation->start_time,
                'end' => $reservation->end_time,
            ];
        });
    }

    /**
     * Enregistre une nouvelle réservation avec validation des règles métier.
     *
     * @param \Illuminate\Http\Request $request Requête contenant les données de la réservation
     * @return \Illuminate\Http\RedirectResponse Redirection avec un message de succès ou d'erreur
     */
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
        $maxYear = $now->year + 2;

        if ($start_time->isBefore($now)) {
            return redirect()->back()->with('error', 'Erreur : Vous ne pouvez pas réserver un créneau passé.');
        }

        if ($end_time->isBefore($start_time)) {
            return redirect()->back()->with('error', 'Erreur : L\'heure de fin ne peut pas être avant l\'heure de début.');
        }

        if ($start_time->diffInMinutes($end_time) < 30) {
            return redirect()->back()->with('error', 'Erreur : La durée minimale est de 30 minutes.');
        }

        if ($start_time->hour < 7 || $end_time->hour > 20 || ($end_time->hour === 20 && $end_time->minute > 0)) {
            return redirect()->back()->with('error', 'Erreur : Les réservations sont autorisées uniquement entre 7h et 20h.');
        }

        if ($start_time->toDateString() !== $end_time->toDateString()) {
            return redirect()->back()->with('error', 'Erreur : Les réservations ne peuvent pas s\'étendre sur plusieurs jours.');
        }

        if ($start_time->year > $maxYear || $end_time->year > $maxYear) {
            return redirect()->back()->with('error', 'Erreur : Vous ne pouvez pas réserver après l\'année ' . $maxYear . '.');
        }

        $overlap = Reservation::where('room_id', $validated['room_id'])
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time, $end_time])
                      ->orWhereBetween('end_time', [$start_time, $end_time])
                      ->orWhere(function ($query) use ($start_time, $end_time) {
                          $query->where('start_time', '<', $end_time)
                                ->where('end_time', '>', $start_time);
                      });
            })
            ->where('end_time', '<>', $start_time)
            ->where('start_time', '<>', $end_time)
            ->exists();

        if ($overlap) {
            return redirect()->back()->with('error', 'Erreur : Ce créneau est déjà réservé.');
        }

        try {
            Reservation::create([
                'user_id' => auth()->id(),
                'room_id' => $validated['room_id'],
                'start_time' => $start_time,
                'end_time' => $end_time,
            ]);

            return redirect()->back()->with('success', 'Votre réservation a été enregistrée.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la création de la réservation.');
        }
    }

    /**
     * Affiche les réservations de l'utilisateur connecté.
     *
     * @return \Illuminate\View\View Vue affichant les réservations de l'utilisateur
     */
    public function userReservations()
    {
        $reservations = Reservation::with('room')
            ->where('user_id', auth()->id())
            ->where('start_time', '>=', Carbon::now())
            ->orderBy('start_time', 'asc')
            ->get();

        return view('reservations.user-reservations', compact('reservations'));
    }

    /**
     * Affiche les détails d'une réservation spécifique.
     *
     * @param int $id Identifiant de la réservation
     * @return \Illuminate\View\View Vue affichant les détails de la réservation
     */
    public function show($id)
    {
        $reservation = Reservation::with('room')->findOrFail($id);

        if ($reservation->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette réservation.');
        }

        return view('reservations.details', compact('reservation'));
    }

    /**
     * Supprime une réservation si l'utilisateur en est propriétaire.
     *
     * @param int $id Identifiant de la réservation
     * @return \Illuminate\Http\RedirectResponse Redirection avec un message de succès ou d'erreur
     */
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
