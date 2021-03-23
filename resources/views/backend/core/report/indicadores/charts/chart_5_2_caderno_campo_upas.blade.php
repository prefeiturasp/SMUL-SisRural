<span>
    @include('backend.components.card-count.index',
        [
            'id'=>'chart_5_2_caderno_campo_upas',
            'title' => 'UPAs com Cadernos de Campo Aplicados',
            'text'=>'',
        ]
    )
</span>

@push('after-scripts')
    <script>
        function chart_5_2_caderno_campo_upas(value) {
            $("#chart_5_2_caderno_campo_upas .txt-text").html(value);
        }
    </script>
@endpush
