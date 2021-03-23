<?php

namespace App\Http\Controllers\Backend\Forms;

use Kris\LaravelFormBuilder\Form;

/**
 * Sobre
 */
class SobreForm extends Form
{
    public function buildForm()
    {
        $this->add('texto', 'hidden');
    }
}
