<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::all(); // Récupère tous les utilisateurs
        return view('users.index', compact('users'));
    }

    /**
     * Afficher les réservations d'un utilisateur spécifique.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showReservations($id)
    {
        $user = User::findOrFail($id); // Vérifie si l'utilisateur existe
        $reservations = Reservation::with('room')->where('user_id', $user->id)->get(); // Récupère ses réservations
        return view('users.reservations', compact('user', 'reservations'));
    }

    /**
     * Supprimer un utilisateur spécifique.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id); // Vérifie si l'utilisateur existe

        // Supprime les réservations associées
        Reservation::where('user_id', $user->id)->delete();

        // Supprime l'utilisateur
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
