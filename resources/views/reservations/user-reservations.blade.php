@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Mes Réservations</h1>

        @if ($reservations->isEmpty())
            <p class="text-muted">Vous n'avez aucune réservation pour le moment.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Salle</th>
                            <th>Heure de début</th>
                            <th>Heure de fin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservations as $reservation)
                            <tr>
                                <td>{{ $reservation->room->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m H:i') }}</td> <!-- Format sans les secondes -->
                                <td>{{ \Carbon\Carbon::parse($reservation->end_time)->format('d/m H:i') }}</td> <!-- Format sans les secondes -->
                                <td>
                                    <!-- Bouton pour supprimer la réservation -->
                                    <form action="{{ route('reservations.destroy', $reservation->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                            Annuler
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
