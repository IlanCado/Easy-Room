import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr';

console.log('Calendar.js is loaded');

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const roomId = calendarEl.dataset.roomId; // Récupère l'ID de la salle depuis le HTML

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
        },
        allDaySlot: false, // Désactivation de la section "Toute la journée"
        locale: 'fr',
        events: `/reservations/${roomId}`, // Charge dynamiquement les réservations pour cette salle
        selectable: true,
        select: function (info) {
            alert('Créneau sélectionné : ' + info.startStr + ' à ' + info.endStr);
        },
    });

    calendar.render();
});
