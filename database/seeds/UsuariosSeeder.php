<?php

use Illuminate\Database\Seeder;
use App\Services\ImportadorService;

class UsuariosSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Faz uma carga inicial dos usuários do sistema através de um XLS.
     *
     * @return void
     */
    public function run(ImportadorService $service)
    {
        $this->disableForeignKeys();

        $filename = 'resources/xlsx/carga_usuarios.xlsx';
        $service->importUsuarios($filename);

        $this->enableForeignKeys();
    }
}
