@if (@$data)
    @if (@$compact)
        @component('backend.core.checklist_unidade_produtiva.components.score-semaforica-compact', ['data'=>$data])
        @endcomponent
    @else
        @component('backend.core.checklist_unidade_produtiva.components.score-semaforica', ['data'=>$data])
        @endcomponent
    @endif
@endif
