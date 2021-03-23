<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Importador p/ produtor/unidade produtiva - Só é acessado pelo Super Admin
 */
class ImportadorForm extends Form
{
    public function buildForm()
    {
        $this->add('card-start', 'card-start', ['title' => 'Importador Produtor vs Unidade Produtiva', 'titleTag' => 'h1']);

        $this->add('arquivo', 'file', ['label' => 'Arquivo', 'rules' => 'required|mimes:xlsx', 'help_block' => ['text' => 'Documento base: resources/xlsx/carga_produtor_unidade_produtiva.xlsx, se rodar duas vezes o mesmo importador, registros com o mesmo ID são ignorados (ELES NÃO SÃO ATUALIZADOS)']]);

        $this->add('card-end', 'card-end');
    }
}
