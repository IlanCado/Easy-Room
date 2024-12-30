@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="my-4">Calendrier des Réservations pour la Salle {{ $room->name }}</h1> 
        <div id="calendar" data-room-id="{{ $room->id }}" style="height: 700px; width: 100%;"></div>
    </div>
@endsection
