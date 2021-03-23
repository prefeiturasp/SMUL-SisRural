<div class="btn-group table-actions">
    @can('edit pergunta checklist')
        <a href="{!! $editUrl !!}" class="btn btn-primary">
            <i class="fa fa-pencil-alt"></i>
        </a>
    @endcan

    @if((@$deleteUrl && Auth::user()->can('delete', @$row)) || (@$respostasUrl && Auth::user()->can('view menu resposta checklist')))
        <div class="btn-group btn-group-sm" role="group">
            <button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                @lang('labels.general.more')
            </button>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userActions">
                {{-- @if($respostasUrl)
                    @can('view menu resposta checklist')
                        <a href="{!! $respostasUrl !!}" class="dropdown-item">Respostas</a>
                    @endcan
                @endif --}}

                @if($deleteUrl)
                    @can('delete', @$row)
                        @can('delete pergunta checklist')
                            <a href="{!! $deleteUrl !!}"
                                data-method="delete"
                                data-trans-button-cancel="@lang('buttons.general.cancel')"
                                data-trans-button-confirm="@lang('buttons.general.crud.delete')"
                                data-trans-title="@lang('strings.backend.general.are_you_sure')"
                                class="dropdown-item">@lang('buttons.general.crud.delete')</a>
                        @endcan
                    @endcan
                @endif
            </div>
        </div>
    @endif
</div>


