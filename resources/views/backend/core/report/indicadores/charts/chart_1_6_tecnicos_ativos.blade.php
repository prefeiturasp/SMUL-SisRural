<span class="cursor-pointer" data-toggle="modal" data-target="#modal_chart_1_6_tecnicos_ativos">
    @include('backend.components.card-count.index',
        [
            'id'=>'chart_1_6_tecnicos_ativos',
            'title' => 'Técnicos/as Ativos',
            'text'=>''
        ]
    )
</span>

@modal(['id'=>'modal_chart_1_6_tecnicos_ativos', 'title'=>'Técnicos/as ativos/as'])
    @slot('body')
        <table class="table table-sm table-ater">
            <thead>
                <tr>
                    <th>Técnico/a</th>
                </tr>
            </thead>
        </table>
    @endslot
@endmodal

@push('after-scripts')
    <script>
        function chart_1_6_tecnicos_ativos(value) {
            $("#chart_1_6_tecnicos_ativos .txt-text").html(value);
        }

        $('#modal_chart_1_6_tecnicos_ativos').on('show.bs.modal', function (e) {
            $('#modal_chart_1_6_tecnicos_ativos .table').DataTable().clear().destroy();

            $('#modal_chart_1_6_tecnicos_ativos .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength":30,
                "ajax": {
                    "url": '{{ route("admin.core.indicadores.dataChart_1_6_TecnicosAtivos") }}',
                    "data": getFilterParams(),
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [
                    {"data": "name"},
                    /*{
                        "data":"actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    },*/
                ]
            });
        });
    </script>
@endpush
