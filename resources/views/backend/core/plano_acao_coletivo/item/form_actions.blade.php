<div class="btn-group table-actions">
    @if (@$viewUrl)
        <a href="{!! $viewUrl !!}" aria-label="Visualizar item" class="btn btn-primary" title="Visualizar item">
            <i class="fas fa-eye"></i>
        </a>
    @endif

    @if (@$editUrl)
        @can('update', @$row)
            <a href="{!! $editUrl !!}" aria-label="Editar" class="btn btn-primary" title="Editar">
                <i class="fa fa-pencil-alt"></i>
            </a>
        @endcan
    @endif

    @if (@$deleteUrl && Auth::user()->can('delete', @$row) || @$createHistoricoUrl || @$reopenUrl && Auth::user()->can('reopen', @$row))
        <div class="btn-group btn-group-sm" role="group">
            <button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                @lang('labels.general.more')
            </button>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userActions"  style="min-width:215px;">
                @if (@$deleteUrl)
                    @can('delete', @$row)
                        <a href="{!! $deleteUrl !!}"
                        data-method="delete"
                        data-trans-button-cancel="@lang('buttons.general.cancel')"
                        data-trans-button-confirm="@lang('buttons.general.crud.delete')"
                        data-trans-title="@lang('strings.backend.general.are_you_sure')"
                        class="dropdown-item">@lang('buttons.general.crud.delete')</a>
                    @endcan
                @endif

                @if (@$createHistoricoUrl)
                    <a href="{!! $createHistoricoUrl !!}" class="dropdown-item btn-create-historico btn-create-historico-item">Acompanhamentos</a>
                @endif

                @if (@$reopenUrl)
                    @can('reopen', @$row)
                        <a href="{!! $reopenUrl !!}"
                            data-method="post"
                            data-trans-button-cancel="Não"
                            data-trans-button-confirm="Sim"
                            data-trans-title="Você tem certeza que deseja reabrir a ação? Todos os dados serão editáveis novamente."
                            class="dropdown-item">Reabrir Ação</a>
                    @endcan
                @endif
            </div>
        </div>
    @endif
</div>

