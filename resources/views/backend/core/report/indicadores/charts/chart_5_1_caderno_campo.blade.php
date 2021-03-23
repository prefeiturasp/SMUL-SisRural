<span>
    @include('backend.components.card-count.index',
        [
            'id'=>'chart_5_1_caderno_campo',
            'title' => 'Cadernos de Campo Aplicados',
            'text'=>'',
        ]
    )
</span>

@push('after-scripts')
    <script>
        function chart_5_1_caderno_campo(value) {
            $("#chart_5_1_caderno_campo .txt-text").html(value);
        }
    </script>
@endpush
