<span class="cursor-pointer" data-toggle="modal" data-target="#modal_chart_1_5_atendimentos_realizados">
    @include('backend.components.card-count.index',
        [
            'id'=>'chart_1_5_atendimentos_realizados',
            'title' => 'Atendimentos Realizados',
            'text'=>''
        ]
    )
</span>

@modal(['id'=>'modal_chart_1_5_atendimentos_realizados', 'title'=>'Atendimentos Realizados'])
    @slot('body')
        <table class="table table-sm table-ater">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Produtor/a</th>
                    <th>Unidade Produtiva</th>
                    <th>Tipo de Atendimento</th>
                    <th>Ação</th>
                </tr>
            </thead>
        </table>
    @endslot
@endmodal

@push('after-scripts')
    <script>
        function chart_1_5_atendimentos_realizados(value) {
            $("#chart_1_5_atendimentos_realizados .txt-text").html(value);
        }

        $('#modal_chart_1_5_atendimentos_realizados').on('show.bs.modal', function (e) {
            $('#modal_chart_1_5_atendimentos_realizados .table').DataTable().clear().destroy();

            $('#modal_chart_1_5_atendimentos_realizados .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength":30,
                "ajax": {
                    "url": '{{ route("admin.core.indicadores.dataChart_1_5_AtendimentosRealizados") }}',
                    "data": getFilterParams(),
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "nome"},
                    {"data": "unidadeProdutiva", "orderable":false},
                    {"data": "type"},
                    {
                        "data":"actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    },
                ]
            });
        });
    </script>
@endpush
