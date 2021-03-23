@php
    if (!$id) {
        $id = uniqid();
    }
@endphp

<div id="btn-confirm-submit-{{$id}}" class="btn btn-primary px-5 btn-confirm-submit" data-toggle="modal" data-target="#confirm-submit-{{$id}}">{{@$label ? $label : 'Salvar'}}</div>

<div class="modal fade" id="confirm-submit-{{$id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Aviso</h5>
            </div>

            <div class="modal-body text-left">
                {!! @$message ? $message : "Você tem certeza que deseja salvar os dados?" !!}
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button class="btn success btn-success btn-primary" data-dismiss="modal">Sim</button>
            </div>
        </div>
    </div>
</div>

@push('after-scripts')
    <script>
        $(document).ready(function() {
            $("#btn-confirm-submit-{{$id}}").click(function() {
                if (window.parent && window.frameElement) {
                    var idIframe = window.frameElement.getAttribute("id");

                    if (idIframe) {
                        var parentIframe = $(window.parent.document).find(
                            "#" + idIframe
                        );

                        $(window.parent.document).scrollTop(
                            parentIframe.offset().top - 150
                        );
                    }
                }
            });

            $("#confirm-submit-{{$id}} .btn-success").click(function() {
                //Não pode por causa da validação que é feita via Javascript
                // $("#{{$form}}").submit();
                // $("#{{$form}}").trigger('submit');

                // Não funciona
                // $("#btn-confirm-submit-{{$id}}").click();
                // <button type="submit" id="btn-confirm-submit-{{$id}}" form="{{$form}}" style="display:none;"></button>

                // O submit é forçado no app.js (porque la ele tem um preventDefault())
                $("#{{$form}}")[0].dispatchEvent(new Event('submit'));
            });
        })
    </script>
@endpush
