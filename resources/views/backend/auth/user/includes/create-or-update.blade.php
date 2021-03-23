@push('after-scripts')
    <script>
        $(function () {
            function hideAll() {
                $("#dominios").parent().parent().addClass("d-none");
                $("#dominios").attr("required", false);

                $("select[name='unidades_operacionais[]']").parent().parent().addClass("d-none");
                $("select[name='unidades_operacionais[]']").attr("required", false);
            };

            $("input[name='roles[]']").change(function() {
                hideAll();

                $("input[name='roles[]']:checked").each(function() {
                    var v = $(this).val();

                    if (v === 'Administrator') {
                        //não faz nada
                    } else if (v === 'Dominio') {
                        $("#dominios").parent().parent().removeClass("d-none");
                        $("#dominios").attr("required", true);
                    } else if (v === 'Unidade Operacional' || v === 'Tecnico') {
                        $("select[name='unidades_operacionais[]']").parent().parent().removeClass("d-none");
                        $("select[name='unidades_operacionais[]']").attr("required", true);
                    }
                })

            }).change();

            $("select[name='unidades_operacionais[]']").select2();
        });
    </script>
@endpush
