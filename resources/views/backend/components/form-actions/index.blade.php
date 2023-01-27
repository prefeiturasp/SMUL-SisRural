<div class="btn-group table-actions">
    @can('update', @$row)
        @if (@$moveOrderUp)
            <a href="{!! $moveOrderUp !!}" data-method="get" class="datatable-form btn btn-primary" title="Mover para cima">
                <i class="fa fa-chevron-up"></i>
            </a>
        @endif

        @if (@$moveOrderDown)
            <a href="{!! $moveOrderDown !!}" data-method="get" class="datatable-form btn btn-primary" title="Mover para baixo">
                <i class="fa fa-chevron-down"></i>
            </a>
        @endif
    @endcan

    @if (@$dashUrl)
        <a href="{!! $dashUrl !!}" aria-label="Buscar" class="btn btn-primary">
            <i class="fas fa-search"></i>
        </a>
    @endif

    @if (@$externalDashUrl)
        <a href="{!! $externalDashUrl !!}" target="_blank" aria-label="Buscar" class="btn btn-primary">
            <i class="fas fa-search"></i>
        </a>
    @endif

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

    @if (@$relationEditUrl)
        @can('update', @$row)
            <a href="{!! $relationEditUrl !!}" aria-label="Editar relação" class="btn btn-primary" title="Editar relação">                
                <i class="fas fa-exchange-alt"></i>
            </a>
        @endcan
    @endif

    @if (@$externalEditUrl)
        @can('update', @$row)
            <a href="{!! $externalEditUrl !!}" target="_blank" aria-label="Editar" class="btn btn-primary" title="Editar">
                <i class="fa fa-pencil-alt"></i>
            </a>
        @endcan
    @endif

    @if (@$addUrl)
        <a href="{!! $addUrl !!}" aria-label="Adicionar" class="btn btn-primary">
            <i class="fa fa-plus"></i>
        </a>
    @endif

    @if ((@$deleteUrl && Auth::user()->can('delete', @$row)) || @$restoreUrl && Auth::user()->can('restore', @$row) || ((@$moveOrderTop || @$moveOrderBack) && Auth::user()->can('update', @$row)) || (@$duplicateUrl && Auth::user()->can('duplicate', @$row)))
        <div class="btn-group btn-group-sm" role="group">
            <button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                @lang('labels.general.more')
            </button>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userActions">
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

                @if (@$restoreUrl)
                    @can('restore', @$row)
                        <a href="{!! $restoreUrl !!}"
                            data-method="post"
                            data-trans-button-cancel="Não"
                            data-trans-button-confirm="Sim"
                            data-trans-title="@lang('strings.backend.general.are_you_sure')"
                            class="dropdown-item">Restaurar</a>
                    @endcan
                @endif

                @can('update', @$row)
                    @if (@$moveOrderTop)
                        <a href="{!! $moveOrderTop !!}" data-method="get" class="datatable-form dropdown-item">Mover para o Início</a>
                    @endif

                    @if (@$moveOrderBack)
                        <a href="{!! $moveOrderBack !!}"data-method="get" class="datatable-form dropdown-item">Mover para o Final</a>
                    @endif
                @endcan

                @if (@$duplicateUrl)
                    @can('duplicate', @$row)
                        <a href="{!! $duplicateUrl !!}"
                        data-method="post"
                        data-trans-button-cancel="Cancelar"
                        data-trans-button-confirm="Sim"
                        data-trans-title="Você deseja duplicar a formulário?"
                        class="dropdown-item">Duplicar</a>
                    @endcan
                @endif
            </div>
        </div>
    @endif
</div>
