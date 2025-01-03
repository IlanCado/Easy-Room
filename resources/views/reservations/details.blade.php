@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Détail de la Réservation</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Salle : {{ $reservation->room->name }}</h5>
            <p><strong>Début :</strong> {{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y H:i') }}</p>
            <p><strong>Fin :</strong> {{ \Carbon\Carbon::parse($reservation->end_time)->format('d/m/Y H:i') }}</p>
            <p><strong>Description de la salle :</strong> {{ $reservation->room->description }}</p>
            <a href="{{ route('reservations.user') }}" class="btn btn-secondary">Retour à mes réservations</a>
        </div>
    </div>
</div>
@endsection
