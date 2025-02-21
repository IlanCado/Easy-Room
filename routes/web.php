<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Page d'accueil affichant la liste des salles disponibles.
 */
Route::get('/', [RoomController::class, 'index'])->name('home');

/**
 * Tableau de bord (non utilisé actuellement).
 * Protéger par le middleware d'authentification.
 */
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/**
 * Routes accessibles uniquement aux utilisateurs authentifiés.
 */
Route::middleware('auth.check')->group(function () {
    /**
     * Gestion du profil utilisateur (édition, mise à jour, suppression).
     */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * Affichage des réservations personnelles de l'utilisateur connecté.
     */
    Route::get('/my-reservations', [ReservationController::class, 'userReservations'])
        ->name('my-reservations');
});

/**
 * Routes accessibles uniquement aux administrateurs.
 */
Route::middleware(['auth.check', 'admin'])->group(function () {
    /**
     * Gestion des salles d'administration.
     */
    Route::get('/admin/rooms', [RoomController::class, 'adminIndex'])->name('admin.rooms.index');
    
    /**
     * Routes CRUD pour les salles (sauf l'affichage public).
     */
    Route::resource('rooms', RoomController::class)->except(['show']);

    /**
     * Routes CRUD pour les équipements.
     */
    Route::resource('equipments', EquipmentController::class);

    /**
     * Gestion des utilisateurs (affichage, suppression et réservations).
     */
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{id}/reservations', [UserController::class, 'showReservations'])->name('users.reservations');
});

/**
 * Routes de gestion des réservations (authentification requise).
 */
Route::prefix('reservations')->middleware('auth.check')->group(function () {
    /**
     * Affichage du calendrier des réservations d'une salle.
     *
     * @param int $roomId ID de la salle
     */
    Route::get('/calendar/{roomId}', [ReservationController::class, 'calendar'])->name('reservations.calendar');

    /**
     * Récupération des réservations d'une salle.
     *
     * @param int $roomId ID de la salle
     * @return \Illuminate\Support\Collection Liste des réservations au format JSON
     */
    Route::get('/{roomId}', [ReservationController::class, 'getReservationsByRoom']);

    /**
     * Création d'une nouvelle réservation.
     *
     * @return \Illuminate\Http\RedirectResponse Redirection avec un message de confirmation
     */
    Route::post('/', [ReservationController::class, 'store'])->name('reservations.store');

    /**
     * Suppression d'une réservation.
     *
     * @param int $id ID de la réservation
     * @return \Illuminate\Http\RedirectResponse Redirection avec un message de confirmation
     */
    Route::delete('/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

    /**
     * Affichage des détails d'une réservation spécifique.
     *
     * @param int $id ID de la réservation
     * @return \Illuminate\View\View Vue affichant les détails de la réservation
     */
    Route::get('/details/{id}', [ReservationController::class, 'show'])->name('reservations.details');

    /**
     * Page de confirmation d'une réservation.
     */
    Route::get('/confirmation', function () {
        return view('reservations.confirmation', [
            'status' => request()->query('status'),
            'message' => request()->query('message'),
            'roomId' => request()->query('roomId'),
        ]);
    })->name('reservations.confirmation');
});

/**
 * Affichage public d'une salle spécifique.
 *
 * @param int $room ID de la salle
 * @return \Illuminate\View\View Vue affichant les détails de la salle
 */
Route::get('/rooms/{room}', [RoomController::class, 'show'])
    ->name('rooms.show')
    ->middleware('auth.check'); // Affichage avec alerte pour les non-authentifiés

/**
 * Inclusion des routes d'authentification générées par Laravel.
 */
require __DIR__ . '/auth.php';
