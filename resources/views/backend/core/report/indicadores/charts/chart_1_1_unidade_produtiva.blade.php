<span class="cursor-pointer" data-toggle="modal" data-target="#modal_chart_1_1_unidade_produtiva">
    @include('backend.components.card-count.index',
        [
            'id'=>'chart_1_1_unidade_produtiva',
            'title' => 'Unidades Produtivas',
            'text'=>'',
        ]
    )
</span>

@modal(['id'=>'modal_chart_1_1_unidade_produtiva', 'title'=>'Unidades Produtivas'])
    @slot('body')
        <table class="table table-sm table-ater">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Unidade Produtiva</th>
                    <th>Produtor/a</th>
                    <th>Ação</th>
            </tr>
            </thead>
        </table>
    @endslot
@endmodal

@push('after-scripts')
    <script>
        function chart_1_1_unidade_produtiva(value) {
            $("#chart_1_1_unidade_produtiva .txt-text").html(value);
        }

        $('#modal_chart_1_1_unidade_produtiva').on('show.bs.modal', function (e) {
            $('#modal_chart_1_1_unidade_produtiva .table').DataTable().clear().destroy();

            $('#modal_chart_1_1_unidade_produtiva .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength":30,
                "ajax": {
                    "url": '{{ route("admin.core.indicadores.dataChart_1_1_UnidadeProdutiva") }}',
                    "data": getFilterParams(),
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "nome"},
                    {"data": "produtores", "orderable":false},
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
