@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Liste des Salles</h1>
        <a href="{{ route('rooms.create') }}" class="btn btn-primary">Ajouter une Salle</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-primary">
                <tr>
                    <th class="text-start">Nom</th>
                    <th class="text-start">Description</th>
                    <th>Capacité</th>
                    <th>Équipements</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rooms as $room)
                    <tr>
                        <td class="text-start fw-bold">{{ $room->name }}</td>
                        <td class="text-start">{{ $room->description }}</td>
                        <td>{{ $room->capacity }} personnes</td>
                        <td>
                            @if ($room->equipments->isNotEmpty())
                                <ul class="list-unstyled mb-0">
                                    @foreach ($room->equipments as $equipment)
                                        <li>{{ $equipment->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">Aucun équipement</span>
                            @endif
                        </td>
                        <td class="d-flex justify-content-center gap-2">
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
                        <td colspan="5" class="text-center text-muted">Aucune salle disponible.</td>
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

    table th, table td {
        vertical-align: middle;
    }
</style>
@endsection
