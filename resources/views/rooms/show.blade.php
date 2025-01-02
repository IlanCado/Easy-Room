@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">{{ $room->name }}</h1>

    <!-- Messages de succès ou d'erreur -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
        height: 'auto',
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
