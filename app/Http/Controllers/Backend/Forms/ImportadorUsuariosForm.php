<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Importador p/ usuários do sistema - Só é acessado pelo Super Admin
 */
class ImportadorUsuariosForm extends Form
{
    public function buildForm()
    {
        $this->add('card-start', 'card-start', ['title' => 'Importador Usuários/as', 'titleTag' => 'h1']);

        $this->add('arquivo', 'file', ['label' => 'Arquivo', 'rules' => 'required|mimes:xlsx', 'help_block' => ['text' => 'Documento base: resources/xlsx/carga_usuarios.xlsx']]);

        $this->add('card-end', 'card-end');
    }
}
