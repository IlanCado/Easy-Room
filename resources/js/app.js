import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

document.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('Élément calendrier non trouvé.');
        return;
    }

    const roomId = calendarEl.dataset.roomId;
    console.log(`Chargement du calendrier pour la salle ID: ${roomId}`);

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        locale: 'fr',
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
        },
        events: `/reservations/${roomId}`,
        slotMinTime: '07:00:00',
        slotMaxTime: '20:00:00',
        scrollTime: '07:00:00',
        allDaySlot: false,
        selectable: true,
        select: function (info) {
            const start = new Date(info.startStr);
            const end = new Date(info.endStr);
            const now = new Date();

            if (start < now) {
                return;
            }

            if (end <= start) {
                return;
            }

            console.log(`📅 Réservation en cours : ${start.toLocaleString()} → ${end.toLocaleString()}`);

            fetch('/reservations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    room_id: roomId,
                    start_time: info.startStr,
                    end_time: info.endStr,
                }),
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    return response.json().then(err => { throw err; });
                })
                .then(data => {
                    console.log('✅ Réservation confirmée:', data);
                    calendar.refetchEvents();
                })
                .catch(error => {
                    console.error('❌ Erreur lors de la réservation:', error);
                });
        },
        eventContent: function (eventInfo) {
            return {
                html: `
                    <span style="font-size: 0.85em; font-weight: bold;">${eventInfo.timeText}</span>
                    <span style="font-size: 0.75em;">${eventInfo.event.title}</span>
                `,
            };
        },
    });

    calendar.render();
});
