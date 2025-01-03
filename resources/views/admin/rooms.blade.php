@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Gestion des Salles</h1>
        <a href="{{ route('rooms.create') }}" class="btn btn-primary">Ajouter une Salle</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Capacité</th>
                    <th>Équipements</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rooms as $room)
                    <tr>
                        <td>{{ $room->id }}</td>
                        <td>
                            @if ($room->image)
                                <img src="{{ asset($room->image) }}" alt="Image de la salle {{ $room->name }}" class="img-thumbnail" style="width: 100px; height: 75px; object-fit: cover;">
                            @else
                                <img src="https://via.placeholder.com/100x75?text=Image+non+disponible" alt="Image par défaut" class="img-thumbnail">
                            @endif
                        </td>
                        <td class="fw-bold">{{ $room->name }}</td>
                        <td>{{ Str::limit($room->description, 50, '...') }}</td>
                        <td>{{ $room->capacity }} personnes</td>
                        <td>
                            @if ($room->equipments->isNotEmpty())
                                <ul class="list-unstyled mb-0">
                                    @foreach ($room->equipments as $equipment)
                                        <li>{{ $equipment->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                Aucun équipement
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-info btn-sm" title="Voir">
                                <i class="bi bi-eye"></i> Voir
                            </a>
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Aucune salle disponible.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    h1 {
        font-size: 1.75rem;
        font-weight: bold;
    }

    .table-primary {
        background-color: #f8f9fa;
    }

    .table th, .table td {
        vertical-align: middle;
    }

    .btn {
        font-size: 0.875rem;
    }

    .img-thumbnail {
        border-radius: 0.25rem;
    }
</style>
@endsection
