<script>
    $(document).ready(function() {
        var cidadesSelect2 = $("select[name='cidade_id']").select2();

        $("#estado_id").change(function() {
                    $.ajax({
                        url:base_url+'api/estados/cidades',
                        method:"GET",
                        data:{
                            id: $(this).val(),
                        }
                    }).done((response)=>{
                        var values = cidadesSelect2.val();

                        var cidades = response.cidades;

                        cidadesSelect2.select2('destroy').empty().select2({
                            data: cidades.map(
                                function(v) {
                                    return {id:v.id, text:v.nome}
                                }
                            ),
                        });

                        cidadesSelect2.val(values).trigger('change');
                    });
        }).change();
    })
</script>
