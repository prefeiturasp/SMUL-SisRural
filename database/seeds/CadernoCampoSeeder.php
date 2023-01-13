<?php

use App\Services\ImportadorService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CadernoCampoSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(ImportadorService $service)
    {
        $createdAt = Carbon::now();

        \App\Models\Core\TemplateModel::insert([
            ['nome' => 'Caderno de Campo', "dominio_id" => 1, "tipo" => "caderno", 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\TemplatePerguntaModel::insert([
            ['id' => 1, 'pergunta' => 'Data da Visita', 'tipo' => 'date', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 2, 'pergunta' => 'Horário de Saída da ETEC', 'tipo' => 'time', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 3, 'pergunta' => 'Horário de Início da Visita', 'tipo' => 'time', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 4, 'pergunta' => 'Horário de Fim da Visita', 'tipo' => 'time', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 5, 'pergunta' => 'Horário de chegada na ETEC', 'tipo' => 'time', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 6, 'pergunta' => 'Descrição das atividades', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 7, 'pergunta' => 'Orientações técnicas', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 8, 'pergunta' => 'Notas', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\TemplatePerguntaTemplatesModel::insert([
            ['template_pergunta_id' => 1, 'template_id' => 1, 'ordem' => '1', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['template_pergunta_id' => 2, 'template_id' => 1, 'ordem' => '2', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['template_pergunta_id' => 3, 'template_id' => 1, 'ordem' => '3', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['template_pergunta_id' => 4, 'template_id' => 1, 'ordem' => '4', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['template_pergunta_id' => 5, 'template_id' => 1, 'ordem' => '5', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['template_pergunta_id' => 6, 'template_id' => 1, 'ordem' => '6', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['template_pergunta_id' => 7, 'template_id' => 1, 'ordem' => '7', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['template_pergunta_id' => 8, 'template_id' => 1, 'ordem' => '8', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);
    }
}
