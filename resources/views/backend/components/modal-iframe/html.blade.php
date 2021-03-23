<div id="{{$id}}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title">{{$title}}</h4>

          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">×</span>
          </button>
        </div>

        <div class="modal-body">
            @include('backend.components.iframe.html', ["id"=>$iframe, "src"=>''])
        </div>
      </div>
    </div>
</div>

@push('after-scripts')
    <script>
        $(document).on('click', '.{{$btnClass}}', function(evt) {
            var href = $(this).attr("href");

            evt.preventDefault();
            evt.stopPropagation();

            var modal = $('#{{$id}}');

            modal.find("iframe").attr("src", '');

            modal.modal({
                backdrop:'static',
            })

            setTimeout(function() {
                modal.find("iframe").attr("src", href);
            }, 200);
        });
    </script>

     @include('backend.components.iframe.scripts', ["id"=>$iframe])
@endpush
