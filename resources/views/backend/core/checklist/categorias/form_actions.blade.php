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

    @if (@$editUrl)
        @can('update', @$row)
            <a href="{!! $editUrl !!}" aria-label="Editar" class="btn btn-primary" title="Editar">
                <i class="fa fa-pencil-alt"></i>
            </a>
        @endcan
    @endif

    @if (@$addPerguntaUrl)
        @can('addPerguntas', @$row)
            <a href="{!! $addPerguntaUrl !!}" aria-label="Editar" class="btn btn-primary text-nowrap" title="Editar">
                + perguntas
            </a>
        @endcan
    @endif

    @if ((@$deleteUrl && Auth::user()->can('delete', @$row)) || ((@$moveOrderTop || @$moveOrderBack) && Auth::user()->can('update', @$row)))
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

                @can('update', @$row)
                    @if (@$moveOrderTop)
                        <a href="{!! $moveOrderTop !!}" data-method="get" class="datatable-form dropdown-item">Mover para o Início</a>
                    @endif

                    @if (@$moveOrderBack)
                        <a href="{!! $moveOrderBack !!}"data-method="get" class="datatable-form dropdown-item">Mover para o Final</a>
                    @endif
                @endcan
            </div>
        </div>
    @endif
</div>
