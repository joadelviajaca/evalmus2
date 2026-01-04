<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Evaluación</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center space-y-6">

        <img
            src="{{ asset('images/logo.svg') }}"
            alt="Logo"
            class="h-20 mx-auto"
        />

        <h1 class="text-2xl font-bold text-primary">
            Sistema de Evaluación de Proyectos
        </h1>

        <p class="text-gray-600">
            Plataforma para la evaluación de proyectos mediante rúbricas
        </p>

        <div class="space-y-3">
            <a
                href="http://localhost:5174/login"
                class="block w-full rounded-lg bg-primary px-4 py-2 text-white font-medium hover:bg-primary/90 transition"
            >
                Acceso evaluadores
            </a>

            <a
                href="/admin/login"
                class="block w-full rounded-lg border border-primary px-4 py-2 text-primary font-medium hover:bg-primary hover:text-white transition"
            >
                Acceso coordinadores / administradores
            </a>
        </div>
    </div>

</body>
</html>
