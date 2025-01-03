@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <!-- Colonne de gauche : Image, Titre, Informations et Réservation -->
        <div class="col-md-4">
            <div class="text-center mb-4">
                <h1 class="display-5 fw-bold text-primary">{{ $room->name }}</h1> <br>
                @if ($room->image)
                    <img src="{{ asset($room->image) }}" 
                         alt="Image de la salle {{ $room->name }}" 
                         class="img-fluid rounded shadow mb-3" 
                         style="width: 100%; height: 300px; object-fit: cover;">
                @else
                    <img src="{{ asset('images/default-room.png') }}" 
                         alt="Image par défaut" 
                         class="img-fluid rounded shadow mb-3" 
                         style="width: 100%; height: 300px; object-fit: cover;">
                @endif
            </div>

            <!-- Informations sur la salle -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Informations sur la salle</h5>
                    <p><strong>Description :</strong> {{ $room->description }}</p>
                    <p><strong>Capacité :</strong> {{ $room->capacity }} personnes</p>
                    @if ($room->equipments->isNotEmpty())
                        <p><strong>Équipements :</strong></p>
                        <ul class="list-unstyled">
                            @foreach ($room->equipments as $equipment)
                                <li>- {{ $equipment->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p><strong>Équipements :</strong> Aucun</p>
                    @endif
                </div>
            </div>

            <!-- Formulaire de réservation -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Poser une réservation</h5>
                    <form action="{{ route('reservations.store') }}" method="POST" id="reservation-form">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $room->id }}">
                        <div class="mb-4">
                            <label for="start_time" class="form-label">Heure de début</label>
                            <input 
                                type="datetime-local" 
                                class="form-control form-control-lg" 
                                id="start_time" 
                                name="start_time" 
                                required>
                        </div>
                        <div class="mb-4">
                            <label for="end_time" class="form-label">Heure de fin</label>
                            <input 
                                type="datetime-local" 
                                class="form-control form-control-lg" 
                                id="end_time" 
                                name="end_time" 
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Réserver</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Colonne de droite : Calendrier -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-center">Calendrier des réservations</h5>
                    <div id="calendar" data-room-id="{{ $room->id }}" style="min-height: 800px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('head-styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('Élément calendrier non trouvé.');
        return;
    }

    const roomId = calendarEl.dataset.roomId;
    const calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['dayGrid', 'timeGrid', 'interaction'],
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        locale: 'fr',
        events: `/reservations/${roomId}`,
        slotMinTime: '07:00:00',
        slotMaxTime: '20:00:00',
        allDaySlot: false,
        height: 900, // Hauteur du calendrier augmentée
        aspectRatio: 1.5, // Rapport largeur/hauteur pour plus de largeur
        selectable: true,
        select: function (info) {
            const start = info.startStr;
            const end = info.endStr;
            if (confirm(`Créer une réservation de ${start} à ${end} ?`)) {
                fetch('/reservations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        room_id: roomId,
                        start_time: start,
                        end_time: end,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.success);
                        calendar.refetchEvents();
                    } else {
                        alert(data.error || 'Erreur inconnue.');
                    }
                })
                .catch(error => alert('Erreur lors de la création de la réservation.'));
            }
        },
    });

    calendar.render();
});
</script>
@endpush
