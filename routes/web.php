<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

// Page d'accueil avec la liste des salles
Route::get('/', [RoomController::class, 'index'])->name('home');

// Tableau de bord protégé par middleware
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes protégées par middleware pour les utilisateurs authentifiés
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/my-reservations', [ReservationController::class, 'userReservations'])->name('my-reservations');
});

// Routes pour les salles
Route::resource('rooms', RoomController::class);

// Routes pour les équipements
Route::resource('equipments', EquipmentController::class);

// Routes pour les réservations
Route::prefix('reservations')->group(function () {
    Route::get('/calendar/{roomId}', [ReservationController::class, 'calendar'])->name('reservations.calendar'); // Afficher le calendrier d'une salle
    Route::get('/{roomId}', [ReservationController::class, 'getReservationsByRoom']); // Récupérer les réservations pour une salle
    Route::post('/', [ReservationController::class, 'store'])->name('reservations.store'); // Créer une nouvelle réservation
    Route::delete('/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy'); // Supprimer une réservation
    Route::get('/confirmation', function () {
        return view('reservations.confirmation', [
            'status' => request()->query('status'),
            'message' => request()->query('message'),
            'roomId' => request()->query('roomId'),
        ]);
    })->name('reservations.confirmation');
});

// Route pour afficher une salle spécifique
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

// Authentification
require __DIR__ . '/auth.php';
