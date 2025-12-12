<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LocaTalk')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 40%, #eef2ff 100%);
            overflow-x: hidden;
            color: #0f172a;
        }
    </style>

    @stack('styles')
</head>
<body class="antialiased">
    @yield('content')

    @stack('scripts')
</body>
</html>
