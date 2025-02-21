<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * Class ProfileController
 * Gère la gestion du profil utilisateur : affichage, mise à jour et suppression.
 *
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{
    /**
     * Affiche le formulaire de modification du profil utilisateur.
     *
     * @param \Illuminate\Http\Request $request Requête contenant l'utilisateur authentifié
     * @return \Illuminate\View\View Vue du formulaire de modification du profil
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Met à jour les informations du profil utilisateur.
     *
     * @param \App\Http\Requests\ProfileUpdateRequest $request Requête validée contenant les nouvelles informations du profil
     * @return \Illuminate\Http\RedirectResponse Redirection vers le formulaire de modification avec un message de confirmation
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        // Si l'email est modifié, annuler la vérification précédente
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Supprime le compte utilisateur.
     *
     * @param \Illuminate\Http\Request $request Requête contenant le mot de passe de confirmation
     * @return \Illuminate\Http\RedirectResponse Redirection vers la page d'accueil après suppression du compte
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        // Invalider la session et régénérer le token de sécurité
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
