<?php

namespace App\Helpers\General;

class DatatablesHelper
{

    /**
     * Recebe o nome do arquivo e o caminho completo do arquivo.
     *
     * Retorna um tag <a> com uma formatacão de imagem / nome
     *
     * Utilizado nos DataTables que possuem arquivos (Unidade Produtiva e Caderno de Campo)
     *
     * @param  string $filename
     * @param  string $fullpath
     * @return void
     */
    public static function renderColumnFile($filename, $fullpath)
    {
        $explode = explode('.', strtolower($filename));
        $extension = array_pop($explode);

        if (in_array($extension, ['png', 'gif', 'jpg', 'jpeg'])) {
            return '<a href="' . $fullpath . '" target="_blank"><img style="width:150px; height:auto;" src="' . $fullpath . '"/></a>';
        } else {
            return '<a href="' . $fullpath . '" target="_blank">' . $filename . '</a>';
        }
    }
}
