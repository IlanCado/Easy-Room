Easy Room - Gestion des Réservations de Salles

📚 Description

Easy Room est une application web permettant aux utilisateurs de réserver des salles de réunion via un calendrier interactif.
L'application est développée en Laravel (backend) et utilise FullCalendar.js (frontend) pour l'affichage des disponibilités des salles.

👉 Fonctionnalités principales

📅 Affichage d'un calendrier dynamique avec toutes les réservations.

✅ Réservation de créneaux horaires par les utilisateurs connectés.

👤 Un utilisateur ne peut voir que ses propres réservations.

🔒 Un administrateur peut gérer toutes les réservations.

🛠️ Gestion des permissions et rôles (utilisateur/admin).

💪 Tests unitaires et fonctionnels avec PHPUnit.

✨ Technologies Utilisées

Backend

Laravel 11 (Framework PHP)

MySQL (Base de données)

PHPUnit (Tests automatisés)

Frontend

FullCalendar.js (Affichage du calendrier interactif)

JavaScript (ES6)

Bootstrap 5 (Stylisation UI)

🏰 Installation & Configuration

1. Prérequis

Avant d'installer le projet, assurez-vous d'avoir :

PHP 8+

Composer

Node.js + npm

MySQL (ou SQLite pour les tests)

2. Cloner le projet

git clone https://github.com/IlanCado/Easy-Room.git

3. Installer les dépendances

composer install
npm install && npm run build

4. Configurer l'environnement

Copiez le fichier .env.example en .env et configurez la base de données :

cp .env.example .env
php artisan key:generate

Modifiez .env avec les informations de votre base de données 

6. Lancer le serveur

php artisan serve

L'application est maintenant accessible sur http://127.0.0.1:8000 🎉

🛠️ Fonctionnalités

📅 Gestion des réservations

✅ Sélectionner une salle et réserver un créneau horaire.

✅ Voir ses propres réservations sur /my-reservations.

❌ Restrictions des réservations :

Interdiction de réserver dans le passé.

L'heure de fin doit être après l'heure de début.

Durée minimale de réservation : 30 minutes.

Les réservations sont possibles uniquement entre 07h00 et 20h00.

Une réservation ne peut pas s’étendre sur plusieurs jours.

Impossible de réserver après l'année actuelle +2 ans.

Un créneau déjà réservé est bloqué.

✅ Annuler une réservation existante.

👤 Gestion des utilisateurs

✅ Inscription & connexion.

✅ Un utilisateur ne peut voir que ses propres réservations.

✅ Un administrateur peut gérer toutes les réservations.

⚙️ Tests & Développement

Des tests unitaires et fonctionnels ont été implémentés avec PHPUnit.

Exécuter les tests

php artisan test

Tests couverts

✅ Un utilisateur peut voir ses réservations.

✅ Un utilisateur peut annuler une réservation.

✅ Un utilisateur ne peut pas réserver dans le passé.

✅ Un utilisateur peut créer une réservation valide.

✅ Un utilisateur ne peut pas supprimer la réservation d'un autre utilisateur.

✅ Un admin peut supprimer n'importe quelle réservation.
