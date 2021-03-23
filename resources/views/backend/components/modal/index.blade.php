<div class="modal fade" id="{{$id}}" tabindex="-1" role="dialog">
    <div class="modal-ater modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{$title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                {{$body}}
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@push('after-styles')
    <style>
        @media (min-width: 576px) {
            .modal-ater {
                max-width: none;
            }
        }

        .modal-ater {
            width: calc(100% - 40px);
            height: 80%;
            padding: 0;
            margin:20px;
        }
    </style>
@endpush
