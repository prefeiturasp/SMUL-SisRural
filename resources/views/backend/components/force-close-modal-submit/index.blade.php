@if(session()->get('flash_success'))
    @push('after-scripts')
        <script>
            $(document).ready(function() {
                $(parent.document.getElementById("modal-create-historico")).find(".close").click();
                $(parent.document.getElementById("modal-create-historico-item")).find(".close").click();
                $(parent.document.getElementById("modal-create-historico-pda")).find(".close").click();
            })
        </script>
    @endpush
@endif
