<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Easy Room - Gestion des Salles</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/calendar.js']) 
</head>
<body>
    <div class="container">
        <!-- Contenu principal -->
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
