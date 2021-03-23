<!DOCTYPE html>
@langrtl
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
@else
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endlangrtl
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', app_name())</title>

        <meta name="description" content="@yield('meta_description', 'SisRural: Sistema de Assistência Rural e Ambiental')">
        <meta name="author" content="@yield('meta_author', '@basedigital')">

        <meta property="og:title" content="@yield('title', app_name())"/>
        <meta property="og:description" content="@yield('meta_description', 'SisRural: Sistema de Assistência Rural e Ambiental')"/>
        <meta property="og:image" content="{{url('/')}}/img/icons/share.png"/>

        <link rel="apple-touch-icon" sizes="57x57" href="/img/icons/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/img/icons/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/img/icons/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/img/icons/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/img/icons/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/img/icons/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/img/icons/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/img/icons/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/img/icons/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/img/icons/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/img/icons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/img/icons/favicon-96x96.png">
        <link rel="icon" sizes="16x16" href="/favicon.ico">

        <link rel="manifest" href="/manifest.json">

        <meta name="theme-color" content="#ffffff">

        @yield('meta')

        {{-- See https://laravel.com/docs/5.5/blade#stacks for usage --}}
        @stack('before-styles')

        <!-- Check if the language is set to RTL, so apply the RTL layouts -->
        <!-- Otherwise apply the normal LTR layouts -->
        {{ style(mix('css/frontend.css')) }}

        @stack('after-styles')

        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
        <link href="https://unpkg.com/@coreui/icons@1.0.0/css/all.min.css" rel="stylesheet" >

        <link href="{{ asset('css/theme.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/app-front.css') }}" rel="stylesheet" />
    </head>
    <body>
        @include('includes.partials.read-only')

        <div id="app">
            @include('includes.partials.logged-in-as')
            @include('frontend.includes.nav')

            <div class="container">
                @include('includes.partials.messages')
                @yield('content')
            </div><!-- container -->
        </div><!-- #app -->

        <!-- Scripts -->
        @stack('before-scripts')
        {!! script(mix('js/manifest.js')) !!}
        <noscript>
            <div role="alert">Para navegar no sistema é necessário habilitar o Javascript</div>
        </noscript>
        {!! script(mix('js/vendor.js')) !!}
        <noscript>
            <div role="alert">Para navegar no sistema é necessário habilitar o Javascript</div>
        </noscript>
        {!! script(mix('js/frontend.js')) !!}
        <noscript>
            <div role="alert">Para navegar no sistema é necessário habilitar o Javascript</div>
        </noscript>
        @stack('after-scripts')



        @include('includes.partials.ga')
    </body>
</html>
