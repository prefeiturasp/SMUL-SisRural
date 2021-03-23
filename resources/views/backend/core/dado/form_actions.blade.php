<div class="btn-group table-actions">
    <a href="{!! $editUrl !!}" aria-label="Editar" class="btn btn-primary">
        <i class="fa fa-pencil-alt"></i>
    </a>

        <div class="btn-group btn-group-sm" role="group">
            <button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                @lang('labels.general.more')
            </button>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userActions">
                <a class="dropdown-item" href="{!! $viewUrl !!}">Dados de acesso</a>

                {{-- <a href="{!! $deleteUrl !!}"
                    data-method="delete"
                    data-trans-button-cancel="@lang('buttons.general.cancel')"
                    data-trans-button-confirm="@lang('buttons.general.crud.delete')"
                    data-trans-title="@lang('strings.backend.general.are_you_sure')"
                    class="dropdown-item">@lang('buttons.general.crud.delete')</a> --}}
            </div>
        </div>
</div>
