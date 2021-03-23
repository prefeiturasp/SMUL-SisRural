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
            ['nome' => 'ATER', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'PSA', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Protocolo Boas Práticas', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Protocolo de Transição', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\UnidadeOperacionalModel::insert([
            ['nome' => 'CAE Zona Sul', "dominio_id" => 1, 'endereco' => '', 'telefone' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'CAE Zona Norte', "dominio_id" => 1, 'endereco' => '', 'telefone' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'PSA Edital', "dominio_id" => 2, 'endereco' => '', 'telefone' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\DominioAbrangenciaEstadosModel::insert([
            ['dominio_id' => 1, 'estado_id' => 35], //são paulo
            ['dominio_id' => 2, 'estado_id' => 43], //rio grande do sul
        ]);

        \App\Models\Core\UnidadeOperacionalAbrangenciaEstadosModel::insert([
            ['unidade_operacional_id' => 1, 'estado_id' => 35], //são paulo
            ['unidade_operacional_id' => 2, 'estado_id' => 35], //são paulo
            ['unidade_operacional_id' => 3, 'estado_id' => 43], //rio grande do sul
        ]);

        $this->enableForeignKeys();
    }
}
