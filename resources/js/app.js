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
            const start = new Date(info.startStr);
            const end = new Date(info.endStr);
            const now = new Date();

            // Vérifie si la réservation commence dans le passé
            if (start < now) {
                alert('Erreur : Vous ne pouvez pas réserver un créneau qui commence dans le passé.');
                return;
            }

            // Vérifie si l'heure de fin est avant l'heure de début
            if (end <= start) {
                alert('Erreur : L\'heure de fin doit être après l\'heure de début.');
                return;
            }

            // Affiche une boîte de dialogue pour confirmation
            if (confirm(`Créer une réservation de ${start.toLocaleString()} à ${end.toLocaleString()} ?`)) {
                // Envoie une requête POST pour créer la réservation
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
                        alert(data.success || 'Réservation créée avec succès.');
                        calendar.refetchEvents(); // Recharge les événements
                    })
                    .catch(error => {
                        // Affiche un message d'erreur détaillé
                        if (error.error) {
                            alert(`Erreur : ${error.error}`);
                        } else {
                            alert('Erreur lors de la création de la réservation. Veuillez réessayer.');
                        }
                    });
            }
        },
        eventContent: function (eventInfo) {
            // Affiche les événements en format compact
            const timeText = `<span style="font-size: 0.85em; font-weight: bold;">${eventInfo.timeText}</span>`;
            const titleText = `<span style="font-size: 0.75em;">${eventInfo.event.title}</span>`;
            return {
                html: `${timeText}<br>${titleText}`,
            };
        },
    });

    calendar.render();
});
