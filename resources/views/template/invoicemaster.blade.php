<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Invoice</title>

    @php
        $faviconUrl = !empty($global_settings['favicon_path']) ? asset($global_settings['favicon_path']) : (!empty($global_settings['logo_path']) ? asset($global_settings['logo_path']) : asset('img/logo/sip.png'));
    @endphp
    {{-- Icon --}}
    <link rel="icon" href="{{ $faviconUrl }}">

    @vite('resources/sass/app.scss')
    @yield('head')
</head>

<body>
    <main class="my-3">
        @yield('content')

    </main>

    @yield('footer')
    @vite('resources/js/app.js')
</body>

</html>
