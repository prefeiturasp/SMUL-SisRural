<?php

use App\Models\Core\DadoModel;
use App\Services\DadoService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

//php artisan db:seed --class=DadosSampaRuralSeeder
class DadosSampaRuralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(DadoService $dadoService)
    {
        $createdAt = Carbon::now();

        \App\Models\Core\DadoModel::insert([
            ['id' => 1, 'nome' => 'Sampa+Rural', 'api_token' => hash('sha256',  'XXX'), 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\DadoAbrangenciaEstadosModel::insert([
            ['dado_id' => 1, 'estado_id' => 35, 'created_at' => $createdAt, 'updated_at' => $createdAt], //Estado de São Paulo
        ]);

        //Sync das abrangências p/ cada acesso da API
        foreach (DadoModel::get() as $v) {
            $dadoService->syncAbrangencias($v);
        }
    }
}
