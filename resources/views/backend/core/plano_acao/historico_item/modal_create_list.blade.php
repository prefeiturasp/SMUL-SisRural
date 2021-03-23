@extends('backend.layouts.app-template', ['iframe'=>true])

@section('body')
    <style>
        body {
            background: transparent;
        }
    </style>

    @cardater
        @slot('body')
            @can('history', $planoAcao)
                @can('create plano_acao_historico')
                    {!! form($form) !!}

                    <div class="row">
                        <div class="col text-right">
                            <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar acompanhamento</button>
                        </div>
                    </div>

                    <br/>
                    <br/>
                @endcan
            @endcan

            <table id="table" class="table table-ater table-sm" style="width:100%;">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Acompanhamento</th>
                        <th>Usuário</th>
                        <th>Adicionado em</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        @endslot
    @endcardater
@endsection

@push('after-scripts')
    @include('backend.components.force-close-modal-submit.index')

    <script>
        $(document).ready(function () {
            $('#table').DataTable({
                "dom": '<"top table-top">rt<"row table-bottom"<"col-sm-12 col-md-5"l><"col-sm-12 col-md-7"p>><"clear">',
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
