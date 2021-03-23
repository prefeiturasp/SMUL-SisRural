<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Importador dos formulários aplicados para as unidades produtivas - Só é acessado pelo Super Admin
 */
class ImportadorChecklistUnidadeProdutivaForm extends Form
{
    public function buildForm()
    {
        $this->add('card-start', 'card-start', ['title' => 'Importador Formulário x Unidade Produtiva', 'titleTag' => 'h1']);

        $this->add('arquivo', 'file', ['label' => 'Arquivo', 'rules' => 'required|mimes:xlsx', 'help_block' => ['text' => 'Documento base: resources/xlsx/carga_checklist_unidade_produtiva.xlsx']]);

        $this->add('card-end', 'card-end');
    }
}
