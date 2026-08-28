<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#071b33">

        <title>{{ $title ?? config('app.name', 'Clínica Benites') }}</title>
        <meta name="description" content="{{ $description ?? 'Clínica Benites: clínica privada en Guayaquil con más de 26 especialidades médicas y quirúrgicas, quirófanos, UCI y UCIN.' }}">

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">

        @fonts

        {{-- Mismo patrón defensivo que welcome.blade.php: si todavía no se
             corrió `npm run build` / no está `vite dev` levantado, no se
             rompe la página — se ve sin estilos en vez de dar error. --}}
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/public.css'])
        @endif
    </head>
    <body class="bg-cb-navy-950 font-sans antialiased">
        {{ $slot }}
    </body>
</html>
