<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! SEO::generate() !!}
    @laravelPWA
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link href="https://map.ir/vector/styles/main/mapir-xyz-light-style.json">
        <link rel="stylesheet" href="{{ mix('css/admin.css') }}">
        <!-- Scripts -->
        @routes
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
        <script src="{{ mix('js/admin.js') }}" defer></script>
    </head>

    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
