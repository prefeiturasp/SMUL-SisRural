<?php

use App\Services\ImportadorService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CadernoCampoCargaSeeder extends Seeder
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

        $filename = 'resources/xlsx/carga_caderno_campo.xlsx';
        $service->importCaderno($filename);

        $this->enableForeignKeys();
    }
}
