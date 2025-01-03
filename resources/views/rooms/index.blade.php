@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Nos Salles</h1>
    </div>
    <div class="row gy-4">
        @forelse ($rooms as $room)
            <div class="col-md-4">
                <a href="{{ route('rooms.show', $room->id) }}" class="card h-100 shadow text-decoration-none text-dark position-relative" style="transition: transform 0.2s;">
                    <div class="card-img-container">
                        @if ($room->image)
                            <img src="{{ asset($room->image) }}" class="card-img-top" alt="Image de la salle {{ $room->name }}" style="height: 200px; object-fit: cover;">
                        @else
                            <img src="https://via.placeholder.com/350x200?text=Image+non+disponible" class="card-img-top" alt="Image par défaut" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="overlay">
                            <span class="btn btn-primary">Obtenez une réservation</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $room->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($room->description, 80, '...') }}</p>
                        <p class="card-text"><strong>Capacité :</strong> {{ $room->capacity }} personnes</p>
                        <p class="card-text">
                            <strong>Équipements :</strong>
                            @if ($room->equipments->isNotEmpty())
                                <ul class="list-unstyled mb-0">
                                    @foreach ($room->equipments as $equipment)
                                        <li>- {{ $equipment->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">Aucun équipement</span>
                            @endif
                        </p>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted">Aucune salle disponible.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    h1 {
        font-size: 1.75rem;
        font-weight: bold;
    }

    .card-title {
        font-size: 1.25rem;
    }

    .card-text {
        font-size: 0.95rem;
    }

    .card {
        position: relative;
        overflow: hidden;
    }

    .card:hover {
        transform: scale(1.02); /* Agrandit légèrement toute la carte au survol */
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2); /* Ajoute une ombre plus marquée */
    }

    .card-img-container {
        position: relative;
    }

    .card-img-top {
        transition: transform 0.2s ease-in-out;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5); /* Fond semi-transparent */
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .card:hover .overlay {
        opacity: 1; /* Affiche l'overlay au survol */
    }

    .overlay span {
        color: white;
        font-size: 1.1rem;
        font-weight: bold;
        padding: 0.5rem 1rem;
        background-color: #007bff; /* Couleur du bouton */
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        text-transform: uppercase;
    }

    .overlay span:hover {
        background-color: #0056b3; /* Couleur plus sombre au survol */
    }
</style>
@endsection
