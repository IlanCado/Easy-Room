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
        initialView: 'timeGridWeek', // Vue initiale
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
        events: `/reservations/${roomId}`, // Récupère les réservations via l'API
        slotMinTime: '07:00:00', // Affiche les créneaux à partir de 7h
        slotMaxTime: '20:00:00', // Affiche les créneaux jusqu'à 20h
        scrollTime: '07:00:00', // Définit l'heure de départ affichée
        allDaySlot: false, // Désactive les événements "toute la journée"
        selectable: true,
        select: function (info) {
            const start = info.startStr;
            const end = info.endStr;

            // Affiche une boîte de dialogue pour confirmation
            if (confirm(`Créer une réservation de ${start} à ${end} ?`)) {
                // Envoie une requête POST pour créer la réservation
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
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        alert(data.success || 'Réservation créée avec succès.');
                        calendar.refetchEvents(); // Recharge les événements
                    })
                    .catch(error => {
                        alert(error.error || 'Erreur lors de la création de la réservation.');
                    });
            }
        },
        eventContent: function (eventInfo) {
            // Retourne un contenu simplifié et compact pour chaque événement
            const timeText = `<span style="font-size: 0.85em; font-weight: bold;">${eventInfo.timeText}</span>`;
            const titleText = `<span style="font-size: 0.75em;">${eventInfo.event.title}</span>`;
            return {
                html: `${timeText}<br>${titleText}`,
            };
        },
        
    });

    calendar.render();
});
