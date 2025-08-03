<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Adidata Global Sistem - CMS')</title>

    {{-- Fonts --}}
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- Font Awesome CDN (latest) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />


    {{-- Favicon --}}
    <link rel="apple-touch-icon" href="{{ asset('assets/img/illustrations/3dlogowotxt.webp') }}" sizes="180x180">
    <link rel="icon" href="{{ asset('assets/img/illustrations/3dlogowotxt.webp') }}" sizes="32x32"
        type="image/x-icon">
    <link rel="icon" href="{{ asset('assets/img/illustrations/3dlogowotxt.webp') }}">
    <link rel="mask-icon" href="{{ asset('assets/img/illustrations/3dlogowotxt.webp') }}" color="#563d7c">
    <meta name="msapplication-config" content="{{ asset('assets/img/illustrations/3dlogowotxt.webp') }}">
    <meta name="theme-color" content="#563d7c">

    {{-- Vite --}}
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">

    {{-- CSS Libraries --}}
    <link rel="stylesheet" href="{{ asset('vendor/apexcharts/apexcharts.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.4/dist/css/datepicker.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.4/dist/css/datepicker-bs4.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/chartist/dist/chartist.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/notyf/notyf.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/volt.css') }}">

    {{-- JS Libraries --}}
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"
        integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.4/dist/js/datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>

    {{-- Local JS Assets --}}
    <script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/onscreen/dist/on-screen.umd.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/nouislider/distribute/nouislider.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/smooth-scroll/dist/smooth-scroll.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/notyf/notyf.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/js/chartist-plugin-tooltip.min.js') }}"></script>
    <script src="{{ asset('assets/js/volt.js') }}"></script>

    {{-- Github buttons --}}
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    @livewireStyles
</head>

<body>
    {{-- SideNav --}}
    @include('layouts.sidenav')

    <main class="content">
        {{-- TopBar --}}
        @include('layouts.topbar')

        {{-- Main Content --}}
        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
        @livewireScripts

        {{-- Footer --}}
        @include('layouts.footer')
    </main>
</body>

</html>
