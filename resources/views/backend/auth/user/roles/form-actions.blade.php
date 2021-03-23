<div class="btn-group table-actions">
    @can('updateRole', @$row)
        <a href="{!! $editUrl !!}" aria-label="Editar" class="btn btn-primary" title="Editar">
            <i class="fa fa-pencil-alt"></i>
        </a>
    @endcan

    @canany(['update', 'deleteRole'], $row)
        @if ($row->id !== auth()->id())
            <div class="btn-group btn-group-sm" role="group">
                <button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    @lang('labels.general.more')
                </button>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userActions">
                    @can('update', $row)
                        @if ($row->id !== auth()->id())
                            @switch($row->active)
                                @case(0)
                                    <a href="{{ route('admin.auth.user.mark', [$row, 1,]) }}" class="dropdown-item">@lang('buttons.backend.access.users.activate')</a>
                                @break

                                @case(1)
                                    <a href="{{ route('admin.auth.user.mark', [$row, 0]) }}" class="dropdown-item">@lang('buttons.backend.access.users.deactivate')</a>
                                @break
                            @endswitch
                        @endif
                    @endcan

                    @can('deleteRole', @$row)
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
    @endcan
</div>
