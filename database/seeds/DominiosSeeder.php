<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DominiosSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $createdAt = Carbon::now();

        $this->disableForeignKeys();

        \App\Models\Core\DominioModel::insert([
            ['nome' => 'Horta em Casa - Maricá', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\UnidadeOperacionalModel::insert([
            ['nome' => 'Maricá - Centro', "dominio_id" => 1, 'endereco' => '', 'telefone' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Ponta Negra', "dominio_id" => 1, 'endereco' => '', 'telefone' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Inoã', "dominio_id" => 1, 'endereco' => '', 'telefone' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Itaipuaçu', "dominio_id" => 1, 'endereco' => '', 'telefone' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\DominioAbrangenciaEstadosModel::insert([
            ['dominio_id' => 1, 'estado_id' => 33], //Rio de Janeiro
        ]);

        \App\Models\Core\UnidadeOperacionalAbrangenciaEstadosModel::insert([
            ['unidade_operacional_id' => 1, 'estado_id' => 33], //Rio de Janeiro
            ['unidade_operacional_id' => 2, 'estado_id' => 33], //Rio de Janeiro
            ['unidade_operacional_id' => 3, 'estado_id' => 33], //Rio de Janeiro
            ['unidade_operacional_id' => 4, 'estado_id' => 33], //Rio de Janeiro
        ]);

        $this->enableForeignKeys();
    }
}
