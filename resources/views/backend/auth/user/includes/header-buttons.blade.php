<div class="btn-toolbar float-right" role="toolbar" aria-label="@lang('labels.general.toolbar_btn_groups')">
    @if (@$showAll)
        <a href="{{ route('admin.auth.user.indexAll') }}" class="btn btn-outline-primary px-5 ml-1 mr-2" data-toggle="tooltip" title="Todos/as os/as Usuáros/as">Todos/as os/as usuários/as</a>
    @endif

    @if ($showDeleted)
        <a href="{{ route('admin.auth.user.deleted') }}" class="btn btn-outline-primary px-5 ml-1 mr-2" data-toggle="tooltip" title="Usuários/as excluídos/as">Usuários/as excluídos/as</a>
    @endif

    @if ($showDesactivated)
        <a href="{{ route('admin.auth.user.deactivated') }}" class="btn btn-outline-primary px-5 ml-1 mr-2" data-toggle="tooltip" title="Usuários desativados">Usuários/as desativados/as</a>
    @endif

    @if ($showActivated)
        <a href="{{ route('admin.auth.user.index') }}" class="btn btn-outline-primary px-5 ml-1 mr-2" data-toggle="tooltip" title="Usuários ativos">Usuários/as ativos/as</a>
    @endif

    @if ($showAdd)
        @can('create', App\Models\Auth\User::class)
            <a href="{{ route('admin.auth.user.create') }}" class="btn btn-primary px-5 ml-1" data-toggle="tooltip" title="@lang('labels.general.create_new')">Adicionar</a>
        @endcan
    @endif
</div><!--btn-toolbar-->
