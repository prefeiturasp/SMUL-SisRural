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

    @if (@$forceDeleteUrl && Auth::user()->can('forceDelete', @$row) || @$restoreUrl && Auth::user()->can('restore', @$row) || @$downloadUrl && Auth::user()->can('view', @$row) || @$sendEmailUrl && Auth::user()->can('sendEmail', @$row) || @$deleteUrl && Auth::user()->can('delete', @$row) || @$checklistUnidadeProdutivaUrl && Auth::user()->can('view', @$row))
        <div class="btn-group btn-group-sm" role="group">
            <button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                @lang('labels.general.more')
            </button>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userActions">
                @if (@$sendEmailUrl)
                    @can('sendEmail', @$row)
                        <a href="{!! $sendEmailUrl !!}" class="dropdown-item">Enviar por e-mail</a>
                    @endcan
                @endif

                @can('view', @$row)
                    @if (@$downloadUrl)
                        <a href="{!! $downloadUrl !!}" data-method="get" class="datatable-form dropdown-item">Fazer download</a>
                    @endif

                    @if (@$checklistUnidadeProdutivaUrl)
                        <a href="{!! $checklistUnidadeProdutivaUrl !!}" class="dropdown-item">Ver Formulário</a>
                    @endif
                @endcan

                @if (@$deleteUrl)
                    @can('delete', @$row)
                        <a href="{!! $deleteUrl !!}"
                        data-method="delete"
                        data-trans-button-cancel="@lang('buttons.general.cancel')"
                        data-trans-button-confirm="@lang('buttons.general.crud.delete')"
                        data-trans-title="{{$messageDelete}}"
                        class="dropdown-item">@lang('buttons.general.crud.delete')</a>
                    @endcan
                @endif

                @if (@$reopenUrl)
                    @can('reopen', @$row)
                        <a href="{!! $reopenUrl !!}"
                            data-method="post"
                            data-trans-button-cancel="Não"
                            data-trans-button-confirm="Sim"
                            data-trans-title="Você tem certeza que deseja reabrir? Todos os dados serão editáveis novamente?"
                            class="dropdown-item">Reabrir Plano de Ação</a>
                    @endcan
                @endif

                @if (@$restoreUrl)
                    @can('restore', @$row)
                        <a href="{!! $restoreUrl !!}"
                            data-method="post"
                            data-trans-button-cancel="Não"
                            data-trans-button-confirm="Sim"
                            data-trans-title="Tem certeza que deseja restaurar?"
                            class="dropdown-item">Restaurar</a>
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
            </div>
        </div>
    @endif
</div>
