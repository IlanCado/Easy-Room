@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">{{ $room->name }}</h1>

    <!-- Layout principal avec deux colonnes -->
    <div class="row">
        <!-- Colonne de gauche : Informations et formulaire -->
        <div class="col-md-4">
            <!-- Informations sur la salle -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Informations</h5>
                    <p><strong>Description:</strong> {{ $room->description }}</p>
                    <p><strong>Capacité:</strong> {{ $room->capacity }} personnes</p>
                    @if ($room->equipments->isNotEmpty())
                        <p><strong>Équipements:</strong></p>
                        <ul>
                            @foreach ($room->equipments as $equipment)
                                <li>{{ $equipment->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p><strong>Équipements:</strong> Aucun</p>
                    @endif
                </div>
            </div>

            <!-- Formulaire de réservation -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Poser une réservation</h5>
                    <form action="{{ route('reservations.store') }}" method="POST" id="reservation-form">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $room->id }}">
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Heure de début</label>
                            <input 
                                type="datetime-local" 
                                class="form-control" 
                                id="start_time" 
                                name="start_time" 
                                required 
                                pattern="\d{4}-\d{2}-\d{2}T\d{2}:\d{2}"
                                placeholder="YYYY-MM-DDTHH:MM"
                            >
                        </div>
                        <div class="mb-3">
                            <label for="end_time" class="form-label">Heure de fin</label>
                            <input 
                                type="datetime-local" 
                                class="form-control" 
                                id="end_time" 
                                name="end_time" 
                                required 
                                pattern="\d{4}-\d{2}-\d{2}T\d{2}:\d{2}"
                                placeholder="YYYY-MM-DDTHH:MM"
                            >
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Réserver</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Colonne de droite : Calendrier -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Calendrier des réservations</h5>
                    <div id="calendar" data-room-id="{{ $room->id }}" style="min-height: 600px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('head-styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
@endpush
