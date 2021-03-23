<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário das Regiões
 *
 * Regiões são arquivos KML cadastrados no sistema, que são consumidos pelas áreas de abrangência do sistema (Domínio e Unidade Operacional)
 *
 */
class RegiaoForm extends Form
{
    public function buildForm()
    {
        $this->add('card-start', 'card-start', [
            'title' => 'Informações principais',
            'titleTag' => 'h1'
        ]);

        $this->add('nome', 'text', [
            'label' => 'Nome',
            'rules' => 'required',
            'error' => __('validation.required', ['attribute' => 'Nome'])
        ]);

        if ($this->model === []) {

            $this->add('poligono', 'file', [
                'label' => 'Região (Arquivo .kml)',
                'rules' => 'required|mimes:kml,xml',
                'error' => __('validation.required', ['attribute' => 'Poligono'])
            ]);
        }

        $this->add('card-end', 'card-end', []);
    }
}
