<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;

/**
 * Class UserController
 * Gère la gestion des utilisateurs : affichage, suppression et visualisation des réservations.
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs.
     *
     * @return \Illuminate\View\View Vue affichant la liste des utilisateurs
     */
    public function index()
    {
        $users = User::all(); // Récupère tous les utilisateurs
        return view('users.index', compact('users'));
    }

    /**
     * Affiche les réservations d'un utilisateur spécifique.
     *
     * @param int $id Identifiant de l'utilisateur
     * @return \Illuminate\View\View Vue affichant les réservations de l'utilisateur
     */
    public function showReservations($id)
    {
        $user = User::findOrFail($id); // Vérifie si l'utilisateur existe
        $reservations = Reservation::with('room')->where('user_id', $user->id)->get(); // Récupère ses réservations
        return view('users.reservations', compact('user', 'reservations'));
    }

    /**
     * Supprime un utilisateur ainsi que ses réservations associées.
     *
     * @param int $id Identifiant de l'utilisateur à supprimer
     * @return \Illuminate\Http\RedirectResponse Redirection avec un message de confirmation
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
