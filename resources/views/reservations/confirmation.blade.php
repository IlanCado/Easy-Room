@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="alert alert-{{ $status === 'success' ? 'success' : 'danger' }}">
        <h4 class="alert-heading">{{ $status === 'success' ? 'Réservation confirmée !' : 'Échec de la réservation.' }}</h4>
        <p>{{ $message }}</p>
    </div>
    <div class="d-flex justify-content-between">
        <a href="{{ route('my-reservations') }}" class="btn btn-primary">Voir Mes Réservations</a>
        <a href="{{ route('reservations.calendar', ['roomId' => $roomId]) }}" class="btn btn-secondary">Retour au Calendrier</a>
    </div>
</div>
@endsection
