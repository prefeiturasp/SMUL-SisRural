{{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> --}}
{{ style(mix('css/backend.css')) }}

<link href="{{ asset('css/app.css') }}" rel="stylesheet" />
<link href="{{ asset('css/components.css') }}" rel="stylesheet" />
<link href="{{ asset('css/theme.css') }}" rel="stylesheet" />

@stack('before-styles')

<style>
     body,html {
        background-color:#FFF;
        margin:0px;
    }
    .page-pdf {
        margin:0px 0px;
    }
    .pdf-header {
        border-bottom:2px solid #E4E7EB;
        padding-bottom:40px;
        margin-bottom:40px;
    }
    .pdf-title {
        font-size:18px;
        font-weight: bold;
        color:#56575A;

        padding:0px 20px;
    }
    .pdf-text {
        font-size:18px;
        color:#56575A;
        padding:20px;
    }
    .table-pdf {
        width:100%;
        color: #56575a;
    }
    .table-pdf thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #d8dbe0;
    }
    .table-pdf td, .table-pdf th {
        padding: .75rem;
        vertical-align: top;
        border-top: 1px solid #d8dbe0;
    }
    .table-pdf.table-sm {
        font-size:14px;
    }
    .table-pdf.table-sm td, .table-pdf.table-sm th {
        padding: .5rem;
    }
    .badge {
        border:0px;
    }
</style>

@stack('after-styles')

<div class="page-pdf">
    <div class="pdf-header">
        <img src="{{URL::asset('/img/email/logo.png')}}" />
    </div>

    @yield('content')

    <div class="text-right">
        <small class="text-muted">Baixado em {{\Carbon\Carbon::now()->format('d/m/Y H:i:s')}}</small>
    </div>
</div>

@stack('before-scripts')

@stack('after-scripts')
