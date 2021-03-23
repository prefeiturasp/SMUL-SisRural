<!DOCTYPE html>
@langrtl
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
@else
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endlangrtl

<head>
    <meta charset="utf-8">
    <base href="{{ url('/').'/'}}"/>
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

    @stack('before-styles')

    {{ style(mix('css/backend.css')) }}

    @stack('after-styles')

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <link href="{{ asset('css/icons-all.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/select2-bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/buttons.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/components.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet" />

    <script type="text/javascript">
        var base_url = "{{ url('/').'/'}}";
        var storage_url = "{{ \Storage::url('/') }}";
    </script>
</head>

<body class="app c-app header-fixed sidebar-fixed aside-menu-off-canvas sidebar-lg-show">


    <div class="app-ater">
        @if(@$iframe)
            @include('includes.partials.messages')
        @endif

        @yield('body')
    </div>

    @stack('before-scripts')
    <script type="text/javascript" src="{{ asset('js/weakmap-polyfill.min.js') }}"></script>

    {!! script(mix('js/manifest.js')) !!}
    {!! script(mix('js/vendor.js')) !!}
    {!! script(mix('js/backend.js')) !!}

    <script type="text/javascript" src="{{ asset('js/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.maskedinput.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-tagsinput.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>

    {{-- <script type="text/javascript" src="{{ asset('js/dataTables.buttons.min.js') }}"></script> --}}
    {{-- <script type="text/javascript" src="{{ asset('js/buttons.bootstrap4.min.js') }}"></script> --}}

    <script type="text/javascript" src="{{ asset('js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/select2.pt-BR.js') }}"></script>
    <script>
        $.fn.select2.defaults.set("theme", "bootstrap");
    </script>

    <script type="text/javascript" src="{{ asset('js/masked.textinput.extend.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/better-dateinput-polyfill.min.js') }}"></script>

    <noscript>É preciso habilitar o javascript para utilizar o sistema</noscript>
    @stack('after-scripts')
    @yield('scripts')
</body>

</html>
