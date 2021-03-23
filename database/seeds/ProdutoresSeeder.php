<?php

use App\Services\ImportadorService;
use Illuminate\Database\Seeder;

class ProdutoresSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Faz uma carga inicial dos produtores/unidades produtivas através de um XLS.
     *
     * @return void
     */
    public function run(ImportadorService $service)
    {
        $this->disableForeignKeys();

        $filename = 'resources/xlsx/carga_produtor_unidade_produtiva.xlsx';
        $service->import($filename);

        $this->enableForeignKeys();
    }
}
