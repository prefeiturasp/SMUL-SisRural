@extends('backend.layouts.app-template', ['iframe'=>true])

@section('body')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-5">
                    <h1 class="card-title mb-0 mt-1 h4">
                        {{$title}}
                    </h1>
                </div>

                {{-- @can('createRespostas', $pergunta) --}}
                <div class="col-sm-7 pull-right">
                    <div class="float-right">
                        <a href="{{ $urlAdd }}"
                        class="btn btn-primary px-5"
                        title="Novo">Adicionar novo acompanhamento</a>
                    </div>
                </div>
                {{-- @endcan --}}
            </div>
        </div>

        <div class="card-body">
            <table id="table" class="table table-ater" style="width:100%">
                <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Acompanhamento</th>
                    <th width="250">Usuário</th>
                    <th width="200">Adicionado em</th>
                    <th></th>
                    <th width="60">Ações</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        $(document).ready(function () {
            $('#table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "pageLength": 30,
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ $urlDatatable }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "texto"},
                    {"data": "usuario"},
                    {"data": "created_at_formatted", "name": "created_at"},
                    {"data": 'created_at_formatted', "name": "created_at_formatted", visible:false},
                    {
                        "data": "actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    }
                ],
                "order": [[ 4, "asc" ]]
            }).on('draw', function () {
                initAutoLink($("#table"));
            });

            addAutoLink(function () {
                debounceSearch('#table');
            });
        });

    </script>
@endpush
