@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Nos Salles</h1>
        @auth
            @if (auth()->user()->role === 'admin') <!-- Vérifie si l'utilisateur est admin -->
                <a href="{{ route('rooms.create') }}" class="btn btn-primary">Ajouter une Salle</a>
            @endif
        @endauth
    </div>

    <div class="row gy-4">
        @forelse ($rooms as $room)
            <div class="col-md-4">
                <div class="card h-100 shadow">
                    @if ($room->image)
                        <img src="{{ asset($room->image) }}" class="card-img-top" alt="Image de la salle {{ $room->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <img src="https://via.placeholder.com/350x200?text=Image+non+disponible" class="card-img-top" alt="Image par défaut" style="height: 200px; object-fit: cover;">
                    @endif
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
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-info btn-sm" title="Voir">
                            <i class="bi bi-eye"></i> Voir
                        </a>
                        @auth
                            @if (auth()->user()->role === 'admin') <!-- Vérifie si l'utilisateur est admin -->
                                <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-warning btn-sm" title="Modifier">
                                    <i class="bi bi-pencil-square"></i> Modifier
                                </a>
                                <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette salle ?')">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
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
</style>
@endsection
