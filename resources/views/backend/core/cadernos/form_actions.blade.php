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

    @can('view', @$row)
        @if (@$viewUrl)
            <a href="{!! $viewUrl !!}" class="btn btn-primary" title="Visualizar">
                <i class="fas fa-eye"></i>
            </a>
        @endif
    @endcan

    @can('update', @$row)
        @if (@$editUrl)
            <a href="{!! $editUrl !!}" class="btn btn-primary" title="Editar">
                <i class="fa fa-pencil-alt"></i>
            </a>
        @endif
    @endcan

    @if ((@$downloadUrl && Auth::user()->can('view', @$row)) || (@$sendEmailUrl && Auth::user()->can('sendEmail', @$row)) ||
         (@$deleteUrl && Auth::user()->can('delete', @$row)) ||
         (@$reanalyseUrl && Auth::user()->can('reanalyse', @$row)) || (@$restoreUrl && Auth::user()->can('restore', @$row)) ||
         (@$planoAcaoUrl && Auth::user()->can('view', @$row)))
        <div class="btn-group btn-group-sm" role="group">
            <button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                @lang('labels.general.more')
            </button>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userActions">
                @can('sendEmail', @$row)
                    @if (@$sendEmailUrl)
                        <a href="{!! $sendEmailUrl !!}" class="dropdown-item">Enviar por e-mail</a>
                    @endif
                @endcan

                @can('view', @$row)
                    @if (@$downloadUrl)
                        <a href="{!! $downloadUrl !!}" data-method="get" class="datatable-form dropdown-item">Fazer download</a>
                    @endif
                @endcan

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

                @if (@$forceDeleteUrl)
                    @can('forceDelete', @$row)
                        <a href="{!! $forceDeleteUrl !!}"
                        data-method="delete"
                        data-trans-button-cancel="@lang('buttons.general.cancel')"
                        data-trans-button-confirm="@lang('buttons.general.crud.delete')"
                        data-trans-title="@lang('strings.backend.general.are_you_sure')"
                        class="dropdown-item">Excluir Definitivamente</a>
                    @endcan
                @endif

                @can('restore', @$row)
                    @if (@$restoreUrl)
                        <a href="{!! $restoreUrl !!}"
                            data-method="post"
                            data-trans-button-cancel="Não"
                            data-trans-button-confirm="Sim"
                            data-trans-title="{!! $messageRestore !!}"
                            class="dropdown-item">Restaurar</a>
                    @endif
                @endcan
            </div>
        </div>
    @endif
 </div>
