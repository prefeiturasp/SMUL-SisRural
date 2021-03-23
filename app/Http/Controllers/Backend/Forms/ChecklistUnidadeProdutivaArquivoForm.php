<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Formulário do Arquivo utilizado no Formulário Aplicado
 */
class ChecklistUnidadeProdutivaArquivoForm extends Form
{
    public function buildForm()
    {
        $this->add(
            'arquivo',
            'file',
            [
                'label' => 'Arquivo',
                'rules' => 'max:25600|mimes:doc,docx,pdf,xls,xlsx,png,jpg,jpeg,gif,txt,kml,shp',
                "maxlength" => 25600,
                'help_block' => [
                    'text' => 'Tamanho máximo do arquivo: 25mb',
                ]
            ]
        )
            ->add('descricao', 'textarea', ['label' => 'Descrição', 'rules' => 'required', 'attr' => ['rows' => 2]]);
        // ->add('lat', 'text', ['label' => 'Latitude', 'small' => 'Latitude vem do arquivo, após salvar.', 'attr' => ['readonly' => 'readonly']])
        // ->add('lng', 'text', ['label' => 'Longitude', 'small' => 'Longitude vem do arquivo, após salvar.', 'attr' => ['readonly' => 'readonly']]);
    }
}
