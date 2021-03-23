<div class="btn-group table-actions">
    @can('edit questao')
        <a href="{!! $editUrl !!}" aria-label="Editar" class="btn btn-primary">
            <i class="fa fa-pencil-alt"></i>
        </a>
    @endcan

    @if((@$deleteUrl && Auth::user()->can('delete questao')) || (@$respostasUrl && Auth::user()->can('view menu resposta caderno')))
        <div class="btn-group btn-group-sm" role="group">
            <button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                @lang('labels.general.more')
            </button>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userActions">
                @if($respostasUrl)
                    @can('view menu resposta caderno')
                        <a href="{!! $respostasUrl !!}" class="dropdown-item">Respostas</a>
                    @endcan
                @endif

                @can('delete questao')
                    <a href="{!! $deleteUrl !!}"
                    data-method="delete"
                    data-trans-button-cancel="@lang('buttons.general.cancel')"
                    data-trans-button-confirm="@lang('buttons.general.crud.delete')"
                    data-trans-title="@lang('strings.backend.general.are_you_sure')"
                    class="dropdown-item">@lang('buttons.general.crud.delete')</a>
                @endcan
            </div>
        </div>
    @endif
</div>
