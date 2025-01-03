<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Page d'accueil avec la liste des salles
Route::get('/', [RoomController::class, 'index'])->name('home');

// Tableau de bord protégé par middleware
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes protégées par middleware pour les utilisateurs authentifiés
Route::middleware('auth.check')->group(function () {
    // Gestion du profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Mes réservations
    Route::get('/my-reservations', [ReservationController::class, 'userReservations'])
        ->name('my-reservations');
});

// Routes protégées par middleware pour les administrateurs uniquement
Route::middleware(['auth.check', 'admin'])->group(function () {
    // Gestion des salles
    Route::get('/admin/rooms', [RoomController::class, 'adminIndex'])->name('admin.rooms.index'); // Page d'administration des salles
    Route::resource('rooms', RoomController::class)->except(['show']); // L'action "show" reste publique

    // Gestion des équipements
    Route::resource('equipments', EquipmentController::class);

    // Gestion des utilisateurs
    Route::get('/users', [UserController::class, 'index'])->name('users.index'); // Afficher tous les utilisateurs
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy'); // Supprimer un utilisateur
    Route::get('/users/{id}/reservations', [UserController::class, 'showReservations'])->name('users.reservations'); // Voir les réservations d'un utilisateur
});

// Routes pour les réservations
Route::prefix('reservations')->middleware('auth.check')->group(function () {
    Route::get('/calendar/{roomId}', [ReservationController::class, 'calendar'])->name('reservations.calendar'); // Afficher le calendrier d'une salle
    Route::get('/{roomId}', [ReservationController::class, 'getReservationsByRoom']); // Récupérer les réservations pour une salle
    Route::post('/', [ReservationController::class, 'store'])->name('reservations.store'); // Créer une nouvelle réservation
    Route::delete('/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy'); // Supprimer une réservation
    Route::get('/details/{id}', [ReservationController::class, 'show'])->name('reservations.details'); // Voir les détails d'une réservation
    Route::get('/confirmation', function () {
        return view('reservations.confirmation', [
            'status' => request()->query('status'),
            'message' => request()->query('message'),
            'roomId' => request()->query('roomId'),
        ]);
    })->name('reservations.confirmation');
});

// Route publique pour afficher une salle spécifique
Route::get('/rooms/{room}', [RoomController::class, 'show'])
    ->name('rooms.show')
    ->middleware('auth.check'); // Afficher une alerte pour les non-authentifiés

// Authentification
require __DIR__ . '/auth.php';
