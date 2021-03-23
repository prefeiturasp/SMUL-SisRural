@if($breadcrumbs)
    <ol class="breadcrumb border-0 m-0 px-0 px-md-3">
        <li class="breadcrumb-item {{
            active_class(Route::is('admin.dashboard'))
        }}">
            <a href="{{ route('admin.dashboard') }}?origin=breadcrumb">Página Inicial</a>
        </li>

        @foreach($breadcrumbs as $breadcrumb)
            @if($breadcrumb->url && !$loop->last)
                <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
            @else
                <li class="breadcrumb-item active">{{ $breadcrumb->title }}</li>
            @endif
        @endforeach
    </ol>
@endif

<style>
    @media (max-width: 767px) {
        .breadcrumb {
            font-size:11px;
            padding: 0px;
            display: flex;
            align-items: center;
        }
        .breadcrumb-item+.breadcrumb-item {
            padding-left:.25rem !important;
        }
    }
</style>
