@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">{{ $room->name }}</h1>
    <div class="row">
        <!-- Informations sur la salle -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Informations</h5>
                    <p><strong>Description:</strong> {{ $room->description }}</p>
                    <p><strong>Capacité:</strong> {{ $room->capacity }} personnes</p>
                    <p><strong>Équipements:</strong></p>
                    @if ($room->equipments->isNotEmpty())
                        <ul>
                            @foreach ($room->equipments as $equipment)
                                <li>{{ $equipment->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>Aucun équipement</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Calendrier -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Calendrier des réservations</h5>
                    <div id="calendar" data-room-id="{{ $room->id }}"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('head-styles')
<!-- Charger les styles FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
@endpush

@push('scripts')
<!-- Charger les scripts FullCalendar -->
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
        events: `/reservations/${roomId}`, // Charge les réservations via l'API
        selectable: true,
        select: function (info) {
            alert(`Créneau sélectionné : de ${info.startStr} à ${info.endStr}`);
        },
    });

    calendar.render();
});
</script>
@endpush
