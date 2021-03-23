<span>
    @include('backend.components.card-count.index',
        [
            'id'=>'chart_1_15_unidade_produtiva_formulario_aplicado',
            'title' => 'UPAs com Formulários Aplicados',
            'text'=>'',
        ]
    )
</span>

@push('after-scripts')
    <script>
        function chart_1_15_unidade_produtiva_formulario_aplicado(value) {
            $("#chart_1_15_unidade_produtiva_formulario_aplicado .txt-text").html(value);
        }
    </script>
@endpush
