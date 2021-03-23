<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Importador - Caderno de campo das unidades produtivas - Só é acessado pelo Super Admin
 */
class ImportadorCadernoForm extends Form
{
    public function buildForm()
    {
        $this->add('card-start', 'card-start', ['title' => 'Importador Caderno de Campo', 'titleTag' => 'h1']);

        $this->add('arquivo', 'file', ['label' => 'Arquivo', 'rules' => 'required|mimes:xlsx', 'help_block' => ['text' => 'Documento base: resources/xlsx/carga_caderno_campo.xlsx']]);

        $this->add('card-end', 'card-end');
    }
}
