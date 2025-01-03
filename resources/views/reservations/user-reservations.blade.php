@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Mes Réservations</h1>

    <!-- Affichage des messages de succès ou d'erreur -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Affichage des réservations sous forme de cartes -->
    @if ($reservations->isEmpty())
        <div class="alert alert-warning" role="alert">
            Vous n'avez aucune réservation pour le moment. Explorez les <a href="{{ route('rooms.index') }}" class="alert-link">salles disponibles</a> pour en réserver une !
        </div>
    @else
        <div class="row">
            @foreach ($reservations as $reservation)
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <!-- Affichage de l'image de la salle -->
                        @if ($reservation->room->image)
                            <img src="{{ asset('storage/' . $reservation->room->image) }}" class="card-img-top" alt="Image de la salle {{ $reservation->room->name }}">
                        @else
                            <img src="https://via.placeholder.com/500x300?text=Aucune+Image" class="card-img-top" alt="Image par défaut">
                        @endif

                        <div class="card-body">
                            <!-- Nom de la salle -->
                            <h5 class="card-title">{{ $reservation->room->name }}</h5>
                            
                            <!-- Informations de la réservation -->
                            <p class="card-text">
                                <strong>Début :</strong> {{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y H:i') }}<br>
                                <strong>Fin :</strong> {{ \Carbon\Carbon::parse($reservation->end_time)->format('d/m/Y H:i') }}
                            </p>
                            
                            <!-- Actions -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('reservations.calendar', $reservation->room->id) }}" class="btn btn-info btn-sm">
                                    Voir le calendrier
                                </a>
                                <form action="{{ route('reservations.destroy', $reservation->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer cette réservation ?');">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    h1 {
        font-size: 2rem;
        font-weight: bold;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .card {
        border-radius: 10px;
        overflow: hidden;
    }

    .card-img-top {
        max-height: 200px;
        object-fit: cover;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: #333;
    }

    .card-text {
        font-size: 0.9rem;
        color: #555;
    }

    .btn-sm {
        font-size: 0.85rem;
    }
</style>
@endsection
