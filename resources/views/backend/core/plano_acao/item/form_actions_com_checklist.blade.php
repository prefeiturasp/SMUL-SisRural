<div class="btn-group table-actions">
    @if (@$viewUrl)
        <a href="{!! $viewUrl !!}" aria-label="Visualizar item" class="btn btn-primary" title="Visualizar item">
            <i class="fas fa-eye"></i>
        </a>
    @endif

    @if (@$detalharAcaoUrl)
        <a href="{!! $detalharAcaoUrl !!}" class="btn btn-secondary btn-detalhar-acao d-flex align-items-center" style="white-space: nowrap">Detalhar ação</a>
    @endif

    @if (@$prioridadeUpUrl)
        <a href="{!! $prioridadeUpUrl !!}" data-method="get" class="datatable-form btn btn-primary" title="Mais Prioridade">
            <i class="fa fa-chevron-up"></i>
        </a>
    @endif

    @if (@$prioridadeDownUrl)
        <a href="{!! $prioridadeDownUrl !!}" data-method="get" class="datatable-form btn btn-primary" title="Menos Prioridade">
            <i class="fa fa-chevron-down"></i>
        </a>
    @endif
</div>

