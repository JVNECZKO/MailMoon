<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MailMoon') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 sm:pt-0">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-700">MailMoon</div>
                <p class="text-sm text-slate-500">Panel kampanii e-mail</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white border border-slate-200 shadow-sm sm:rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
