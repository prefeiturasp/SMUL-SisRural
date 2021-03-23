@extends('backend.layouts.app')

@section('title', app_name() . ' | Perguntas')

@section('content')
<div class="card card-ater">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-5">
                <h1 class="card-title mb-0 mt-1 h4">
                    Termos de Uso
                </h1>
            </div>

            {{-- <div class="col-sm-7 pull-right">
                <div class="float-right">
                    <a href="{{ route('admin.core.termos_de_uso.edit', ['termosDeUso'=>1]) }}" class="btn btn-primary px-5" title="Novo">Editar Termos de Uso</a>
                </div>
            </div> --}}
        </div>
    </div>

    <div class="card-body">
        <table id="table" class="table table-ater">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Texto</th>
                    <th width="60">Ações</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
    $(document).ready(function() {
        var table = $('#table').DataTable({
            "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
            "processing": true,
            "serverSide": true,
            "lengthChange": false,
            "ajax": '{{ route('admin.core.termos_de_uso.datatable') }}',
            "language": {
                "url": '{{ asset('js/datatables-pt-br.json')}}'
            },
            "columns": [{
                    "data": "id"
                },
                {
                    "data": "texto"
                },
                {
                    "data": "actions",
                    "searchable": false,
                    "orderable": false,
                    render: function(data) {
                        return htmlDecode(data);
                    }
                }
            ]
        });

        table.on("draw", function() {
            initAutoLink($("#table"));
        });

        addAutoLink(function() {
            debounceSearch('#table');
        });
    });
</script>
@endpush
