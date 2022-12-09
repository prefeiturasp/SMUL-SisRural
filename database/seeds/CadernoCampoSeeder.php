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
            // Não precisa mais essa informação
            //['id' => 1, 'pergunta' => 'Versão do Caderno', 'tipo' => 'text', 'tags' => 'versão', 'created_at' => $createdAt, 'updated_at' => $createdAt],

            // Created_at no Caderno
            // ['id' => 2, 'pergunta' => 'Data Coleta', 'tipo' => 'text', 'tags' => 'coleta', 'created_at' => $createdAt, 'updated_at' => $createdAt],

            // Finished_at no Caderno
            // ['pergunta' => 'Data Envio', 'tipo' => 'text', 'tags' => 'envio', 'created_at' => $createdAt, 'updated_at' => $createdAt],

            // User_id no Caderno
            // ['pergunta' => 'Usuário', 'tipo' => 'text', 'tags' => 'usuário', 'created_at' => $createdAt, 'updated_at' => $createdAt],

            // Lat/lng vai ser utilizado da Unidade Produtiva
            // ['id' => 3, 'pergunta' => 'Latitude', 'tipo' => 'text', 'tags' => 'latitude', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            // ['id' => 4, 'pergunta' => 'Longitude', 'tipo' => 'text', 'tags' => 'longitude', 'created_at' => $createdAt, 'updated_at' => $createdAt],

            // Dados do Produtor / Unidade Produtiva (já estão no caderno)
            // ['pergunta' => 'Nome do Agricultor', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            // ['pergunta' => 'ID Agricultor', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            // ['pergunta' => 'Unidade Produtiva', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            // ['pergunta' => 'ID Unidade Produtiva', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],

            ['id' => 1, 'pergunta' => 'Data da Visita', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 2, 'pergunta' => 'Técnico/a (s)', 'tipo' => 'multiple_check', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 3, 'pergunta' => 'Tipo Atividade', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 4, 'pergunta' => 'Finalidade da Atividade', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 5, 'pergunta' => 'Áreas / Talhões', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 6, 'pergunta' => 'Recomendações', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 7, 'pergunta' => 'Demandas do Agricultor', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 8, 'pergunta' => 'Programas CAE-LoP', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 9, 'pergunta' => 'Observações', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 10, 'pergunta' => 'Registro dos Avanços', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 11, 'pergunta' => 'Confirmação de Telefones, CPF e Endereço', 'tipo' => 'text', 'tags' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\TemplateRespostaModel::insert([
            ['id' => 1, 'template_pergunta_id' => 2, 'descricao' => 'João Vitor', 'ordem' => '1', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 2, 'template_pergunta_id' => 2, 'descricao' => 'Ronaldo', 'ordem' => '2', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 3, 'template_pergunta_id' => 2, 'descricao' => 'Mauro Kayano', 'ordem' => '3', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 4, 'template_pergunta_id' => 2, 'descricao' => 'Tiago Gomes', 'ordem' => '4', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 5, 'template_pergunta_id' => 2, 'descricao' => 'Rubia', 'ordem' => '5', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 6, 'template_pergunta_id' => 2, 'descricao' => 'Jonatas', 'ordem' => '6', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 7, 'template_pergunta_id' => 2, 'descricao' => 'Vicente', 'ordem' => '7', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 8, 'template_pergunta_id' => 2, 'descricao' => 'Robson', 'ordem' => '8', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 9, 'template_pergunta_id' => 2, 'descricao' => 'Aline', 'ordem' => '9', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 10, 'template_pergunta_id' => 2, 'descricao' => 'Cris Gomes', 'ordem' => '10', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 11, 'template_pergunta_id' => 2, 'descricao' => 'Cris Mendes', 'ordem' => '11', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 20, 'template_pergunta_id' => 2, 'descricao' => 'Cris Mendes', 'ordem' => '12', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 21, 'template_pergunta_id' => 2, 'descricao' => 'Cris Mendes', 'ordem' => '13', 'created_at' => $createdAt, 'updated_at' => $createdAt],
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
            ['template_pergunta_id' => 9, 'template_id' => 1, 'ordem' => '9', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['template_pergunta_id' => 10, 'template_id' => 1, 'ordem' => '10', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['template_pergunta_id' => 11, 'template_id' => 1, 'ordem' => '11', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);
    }
}
