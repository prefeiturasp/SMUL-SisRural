@extends('backend.layouts.app-template', ['iframe'=>true])

@section('body')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-5">
                    <h1 class="card-title mb-0 mt-1 h4">
                        {{$title}}
                    </h1>
                </div>

                <div class="col-sm-7 pull-right">
                    <div class="float-right">
                        <a href="{{ $urlAdd }}"
                           class="btn btn-primary px-5"
                           title="Novo">Vincular Outra Pergunta</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table id="table" class="table table-striped table-bordered" style="width:100%">
                <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Pergunta</th>
                    <th>Respostas</th>
                    <th>Ordem</th>
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
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ $urlDatatable }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "id"},
                    {"data": "pergunta"},
                    {"data": "respostas"},
                    {"data": "ordem"},
                    {
                        "data": "actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    }
                ],
                "order": [[ 3, "asc" ]]
            }).on('draw', function () {
                initAutoLink($("#table"));
            });

            addAutoLink(function () {
                debounceSearch('#table');
            });
        });

    </script>
@endpush
