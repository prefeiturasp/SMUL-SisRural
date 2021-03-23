<?php

use App\Services\ImportadorService;
use Illuminate\Database\Seeder;

class ChecklistCargaSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(ImportadorService $service)
    {
        $this->disableForeignKeys();

        $filename = 'resources/xlsx/carga_checklist_unidade_produtiva.xlsx';
        $service->importChecklistUnidadeProdutiva($filename);

        $this->enableForeignKeys();
    }
}
