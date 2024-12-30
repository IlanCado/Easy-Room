<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ReservationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Routes pour les salles
Route::resource('rooms', RoomController::class);

// Routes pour les équipements
Route::resource('equipments', EquipmentController::class);

// Routes pour les réservations
Route::get('/calendar/{roomId}', [ReservationController::class, 'calendar'])->name('reservations.calendar'); // Afficher le calendrier d'une salle
Route::get('/reservations/{roomId}', [ReservationController::class, 'getReservationsByRoom']); // Récupérer les réservations pour une salle
Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store'); // Créer une nouvelle réservation
