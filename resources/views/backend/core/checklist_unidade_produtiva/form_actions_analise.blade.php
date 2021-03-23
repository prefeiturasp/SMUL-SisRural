<div class="btn-group table-actions">
    @can('view', @$row)
        @if (@$viewUrl)
            @if ($row->status == \App\Enums\ChecklistStatusEnum::AguardandoAprovacao)
                @can('analize', $row)
                    <a href="{!! $viewUrl !!}" class="btn btn-primary" title="Analisar">
                        <i class="fas fa-check"></i>
                    </a>
                @else
                    <a href="{!! $viewUrl !!}" class="btn btn-primary" title="Visualizar">
                        <i class="fas fa-eye"></i>
                    </a>
                @endcan
            @else
                <a href="{!! $viewUrl !!}" class="btn btn-primary" title="Visualizar">
                    <i class="fas fa-eye"></i>
                </a>
            @endif
        @endif
    @endcan

    @can('reanalyse', @$row)
        <div class="btn-group btn-group-sm" role="group">
            <button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                @lang('labels.general.more')
            </button>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userActions">
                <a href="{!! $reanalyseUrl !!}"
                    data-method="post"
                    data-trans-button-cancel="Não"
                    data-trans-button-confirm="Sim"
                    data-trans-title="Você tem certeza que deseja reanalisar a aplicação do formulário?"
                    class="dropdown-item">Reanalisar Aplicação</a>
            </div>
        </div>
    @endcan
 </div>
