<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Execução direta: php artisan db:seed --class=ChecklistSeederBase
 *
 * Se for importado o "ChecklistProtocoloTransicaoSeeder" não deve ser importado esse arquivo, porque vai conflitar os IDS
 *
 * Esse arquivo é mais antigo, utilizado p/ testes do sistema
 */
class ChecklistSeederBase extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $createdAt = Carbon::now();

        \DB::table('perguntas')->insert(array(
            0 =>
            array(
                'id' => 48,
                'tipo_pergunta' => 'semaforica',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Semafórica',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 1',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'id' => 49,
                'tipo_pergunta' => 'semaforica-cinza',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Semafórica com Cinza',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 2',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'id' => 50,
                'tipo_pergunta' => 'binaria',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Binária',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 3',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            3 =>
            array(
                'id' => 51,
                'tipo_pergunta' => 'binaria-cinza',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Binária com Cinza',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 4',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            4 =>
            array(
                'id' => 52,
                'tipo_pergunta' => 'numerica-pontuacao',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Numérica com Pontuação (multiplica peso 10)',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 5',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            5 =>
            array(
                'id' => 53,
                'tipo_pergunta' => 'numerica',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Numérica',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 6',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            6 =>
            array(
                'id' => 54,
                'tipo_pergunta' => 'texto',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Texto',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 7',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            7 =>
            array(
                'id' => 55,
                'tipo_pergunta' => 'tabela',
                'tabela_colunas' => 'coluna 1,coluna 2,coluna 3',
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Tabela',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 8',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            8 =>
            array(
                'id' => 56,
                'tipo_pergunta' => 'tabela',
                'tabela_colunas' => 'coluna 1,coluna 2,coluna 3',
                'tabela_linhas' => 'linha 1,linha 2,linha 3',
                'pergunta' => 'Pergunta Tabela com Linha',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 9 ',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            9 =>
            array(
                'id' => 57,
                'tipo_pergunta' => 'multipla-escolha',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Multipla Escolha',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 10',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            10 =>
            array(
                'id' => 58,
                'tipo_pergunta' => 'escolha-simples',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Escolha Simples',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 11',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            11 =>
            array(
                'id' => 59,
                'tipo_pergunta' => 'escolha-simples-pontuacao',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Escolha Simples com Pontuação',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 12 ',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            12 =>
            array(
                'id' => 60,
                'tipo_pergunta' => 'anexo',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Anexo',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 13 ',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            13 =>
            array(
                'id' => 61,
                'tipo_pergunta' => 'texto',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Texto Arquivada',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 1,
                'plano_acao_default' => 'Plano de ação 14',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            14 =>
            array(
                'id' => 62,
                'tipo_pergunta' => 'texto',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta Texto Geral',
                'texto_apoio' => 'Texto de Apoio',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'Plano de ação 15',
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            15 =>
            array(
                'id' => 63,
                'tipo_pergunta' => 'semaforica',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'tste',
                'texto_apoio' => 'test',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'eteste',
                'tags' => 'tste',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            16 =>
            array(
                'id' => 64,
                'tipo_pergunta' => 'binaria',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'Pergunta teste policy',
                'texto_apoio' => NULL,
                'fl_arquivada' => 0,
                'plano_acao_default' => NULL,
                'tags' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            17 =>
            array(
                'id' => 65,
                'tipo_pergunta' => 'semaforica',
                'tabela_colunas' => NULL,
                'tabela_linhas' => NULL,
                'pergunta' => 'pergunta para testar os policies',
                'texto_apoio' => 'asd',
                'fl_arquivada' => 0,
                'plano_acao_default' => 'asd',
                'tags' => 'sad',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
        ));

        // \App\Models\Core\PerguntaModel::insert([
        //     ['id' => 1, 'tipo_pergunta' => 'semaforica', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Semafórica", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 2, 'tipo_pergunta' => 'semaforica-cinza', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Semafórica com Cinza", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 3, 'tipo_pergunta' => 'binaria', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Binária", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 4, 'tipo_pergunta' => 'binaria-cinza', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Binária com Cinza", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 5, 'tipo_pergunta' => 'numerica-pontuacao', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Numérica com Pontuação (multiplica peso 10)", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 6, 'tipo_pergunta' => 'numerica', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Numérica", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 7, 'tipo_pergunta' => 'texto', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Texto", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 8, 'tipo_pergunta' => 'tabela', "tabela_colunas" => "coluna 1,coluna 2,coluna 3", "tabela_linhas" => null, "pergunta" => "Pergunta Tabela", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 9, 'tipo_pergunta' => 'tabela', "tabela_colunas" => "coluna 1,coluna 2,coluna 3", "tabela_linhas" => "linha 1,linha 2,linha 3", "pergunta" => "Pergunta Tabela com Linha", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 10, 'tipo_pergunta' => 'multipla-escolha', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Multipla Escolha", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 11, 'tipo_pergunta' => 'escolha-simples', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Escolha Simples", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 12, 'tipo_pergunta' => 'escolha-simples-pontuacao', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Escolha Simples com Pontuação", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 13, 'tipo_pergunta' => 'anexo', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Anexo", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 14, 'tipo_pergunta' => 'texto', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Texto Arquivada", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 15, 'tipo_pergunta' => 'texto', "tabela_colunas" => null, "tabela_linhas" => null, "pergunta" => "Pergunta Texto Geral", "plano_acao_default" => "Plano de ação", "texto_apoio" => "Texto de Apoio", "fl_arquivada" => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        // ]);

        \DB::table('respostas')->insert(array(
            0 =>
            array(
                'id' => 176,
                'pergunta_id' => 48,
                'descricao' => 'Resposta Amarelo',
                'cor' => 'amarelo',
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'id' => 177,
                'pergunta_id' => 48,
                'descricao' => 'Resposta Verde',
                'cor' => 'verde',
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'id' => 178,
                'pergunta_id' => 48,
                'descricao' => 'Resposta Vermelho',
                'cor' => 'vermelho',
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 3,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            3 =>
            array(
                'id' => 179,
                'pergunta_id' => 49,
                'descricao' => 'Resposta Verde',
                'cor' => 'verde',
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            4 =>
            array(
                'id' => 180,
                'pergunta_id' => 49,
                'descricao' => 'Resposta Amarelo',
                'cor' => 'amarelo',
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            5 =>
            array(
                'id' => 181,
                'pergunta_id' => 49,
                'descricao' => 'Resposta Vermelho',
                'cor' => 'vermelho',
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 3,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            6 =>
            array(
                'id' => 182,
                'pergunta_id' => 49,
                'descricao' => 'Resposta Cinza',
                'cor' => 'cinza',
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 4,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            7 =>
            array(
                'id' => 183,
                'pergunta_id' => 50,
                'descricao' => 'Resposta Verde',
                'cor' => 'verde',
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            8 =>
            array(
                'id' => 184,
                'pergunta_id' => 50,
                'descricao' => 'Resposta Vermelho',
                'cor' => 'vermelho',
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            9 =>
            array(
                'id' => 185,
                'pergunta_id' => 51,
                'descricao' => 'Resposta Verde',
                'cor' => 'verde',
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            10 =>
            array(
                'id' => 186,
                'pergunta_id' => 51,
                'descricao' => 'Resposta Vermelho',
                'cor' => 'vermelho',
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            11 =>
            array(
                'id' => 187,
                'pergunta_id' => 51,
                'descricao' => 'Resposta Cinza',
                'cor' => 'cinza',
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 3,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            12 =>
            array(
                'id' => 188,
                'pergunta_id' => 57,
                'descricao' => 'Gato',
                'cor' => NULL,
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            13 =>
            array(
                'id' => 189,
                'pergunta_id' => 57,
                'descricao' => 'Cachorro',
                'cor' => NULL,
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            14 =>
            array(
                'id' => 190,
                'pergunta_id' => 57,
                'descricao' => 'Passarinho',
                'cor' => NULL,
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 3,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            15 =>
            array(
                'id' => 191,
                'pergunta_id' => 58,
                'descricao' => 'Sim',
                'cor' => NULL,
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            16 =>
            array(
                'id' => 192,
                'pergunta_id' => 58,
                'descricao' => 'Não',
                'cor' => NULL,
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            17 =>
            array(
                'id' => 193,
                'pergunta_id' => 58,
                'descricao' => 'Talvez',
                'cor' => NULL,
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 3,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            18 =>
            array(
                'id' => 194,
                'pergunta_id' => 59,
                'descricao' => 'Soma 10',
                'cor' => NULL,
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            19 =>
            array(
                'id' => 195,
                'pergunta_id' => 59,
                'descricao' => 'Soma 5',
                'cor' => NULL,
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            20 =>
            array(
                'id' => 196,
                'pergunta_id' => 59,
                'descricao' => 'Soma 1',
                'cor' => NULL,
                'texto_apoio' => 'Texto de Apoio',
                'ordem' => 3,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            21 =>
            array(
                'id' => 197,
                'pergunta_id' => 63,
                'descricao' => 'teste',
                'cor' => 'verde',
                'texto_apoio' => NULL,
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            22 =>
            array(
                'id' => 198,
                'pergunta_id' => 63,
                'descricao' => 'asd',
                'cor' => 'amarelo',
                'texto_apoio' => NULL,
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            23 =>
            array(
                'id' => 199,
                'pergunta_id' => 63,
                'descricao' => 'asd',
                'cor' => 'vermelho',
                'texto_apoio' => NULL,
                'ordem' => 3,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            24 =>
            array(
                'id' => 200,
                'pergunta_id' => 64,
                'descricao' => 'resposta 1',
                'cor' => 'verde',
                'texto_apoio' => NULL,
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            25 =>
            array(
                'id' => 201,
                'pergunta_id' => 64,
                'descricao' => 'resposta 2',
                'cor' => 'vermelho',
                'texto_apoio' => NULL,
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            26 =>
            array(
                'id' => 203,
                'pergunta_id' => 65,
                'descricao' => 'amarelo',
                'cor' => 'amarelo',
                'texto_apoio' => NULL,
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
        ));

        // \App\Models\Core\RespostaModel::insert([
        //     ['id' => 1, 'pergunta_id' => 1, "descricao" => 'Resposta Verde', "cor" => CorEnum::Verde, "texto_apoio" => "Texto de Apoio", "ordem" => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 2, 'pergunta_id' => 1, "descricao" => 'Resposta Amarelo', "cor" => CorEnum::Amarelo, "texto_apoio" => "Texto de Apoio", "ordem" => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 3, 'pergunta_id' => 1, "descricao" => 'Resposta Vermelho', "cor" => CorEnum::Vermelho, "texto_apoio" => "Texto de Apoio", "ordem" => 3, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     ['id' => 4, 'pergunta_id' => 2, "descricao" => 'Resposta Verde', "cor" => CorEnum::Verde, "texto_apoio" => "Texto de Apoio", "ordem" => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 5, 'pergunta_id' => 2, "descricao" => 'Resposta Amarelo', "cor" => CorEnum::Amarelo, "texto_apoio" => "Texto de Apoio", "ordem" => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 6, 'pergunta_id' => 2, "descricao" => 'Resposta Vermelho', "cor" => CorEnum::Vermelho, "texto_apoio" => "Texto de Apoio", "ordem" => 3, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 7, 'pergunta_id' => 2, "descricao" => 'Resposta Cinza', "cor" => CorEnum::Cinza, "texto_apoio" => "Texto de Apoio", "ordem" => 4, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     ['id' => 8, 'pergunta_id' => 3, "descricao" => 'Resposta Verde', "cor" => CorEnum::Verde, "texto_apoio" => "Texto de Apoio", "ordem" => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 9, 'pergunta_id' => 3, "descricao" => 'Resposta Vermelho', "cor" => CorEnum::Vermelho, "texto_apoio" => "Texto de Apoio", "ordem" => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     ['id' => 10, 'pergunta_id' => 4, "descricao" => 'Resposta Verde', "cor" => CorEnum::Verde, "texto_apoio" => "Texto de Apoio", "ordem" => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 11, 'pergunta_id' => 4, "descricao" => 'Resposta Vermelho', "cor" => CorEnum::Vermelho, "texto_apoio" => "Texto de Apoio", "ordem" => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 12, 'pergunta_id' => 4, "descricao" => 'Resposta Cinza', "cor" => CorEnum::Cinza, "texto_apoio" => "Texto de Apoio", "ordem" => 3, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     ['id' => 13, 'pergunta_id' => 10, "descricao" => 'Gato', "cor" => null, "texto_apoio" => "Texto de Apoio", "ordem" => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 14, 'pergunta_id' => 10, "descricao" => 'Cachorro', "cor" => null, "texto_apoio" => "Texto de Apoio", "ordem" => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 15, 'pergunta_id' => 10, "descricao" => 'Passarinho', "cor" => null, "texto_apoio" => "Texto de Apoio", "ordem" => 3, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     ['id' => 16, 'pergunta_id' => 11, "descricao" => 'Sim', "cor" => null, "texto_apoio" => "Texto de Apoio", "ordem" => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 17, 'pergunta_id' => 11, "descricao" => 'Não', "cor" => null, "texto_apoio" => "Texto de Apoio", "ordem" => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 18, 'pergunta_id' => 11, "descricao" => 'Talvez', "cor" => null, "texto_apoio" => "Texto de Apoio", "ordem" => 3, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     ['id' => 19, 'pergunta_id' => 12, "descricao" => 'Soma 10', "cor" => null, "texto_apoio" => "Texto de Apoio", "ordem" => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 20, 'pergunta_id' => 12, "descricao" => 'Soma 5', "cor" => null, "texto_apoio" => "Texto de Apoio", "ordem" => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 21, 'pergunta_id' => 12, "descricao" => 'Soma 1', "cor" => null, "texto_apoio" => "Texto de Apoio", "ordem" => 3, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        // ]);

        \DB::table('checklists')->insert(array(
            0 =>
            array(
                'id' => 2,
                'dominio_id' => 1,
                'copia_checklist_id' => NULL,
                'versao' => 0,
                'nome' => 'Checklist Semafórica/Binária',
                'instrucoes' => NULL,
                'fl_fluxo_aprovacao' => 0,
                'status' => 'publicado',
                'plano_acao' => 'opcional',
                'formula' => '(0.25405)*(((1+C13)*C14)+((1+C13)*C14))',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
                'tipo_pontuacao' => 'com_pontuacao_formula_personalizada',
            ),
            1 =>
            array(
                'id' => 3,
                'dominio_id' => 1,
                'copia_checklist_id' => NULL,
                'versao' => 0,
                'nome' => 'Checklist Pontuação Simples',
                'instrucoes' => NULL,
                'fl_fluxo_aprovacao' => 1,
                'status' => 'publicado',
                'plano_acao' => 'opcional',
                'formula' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
                'tipo_pontuacao' => 'com_pontuacao',
            ),
            2 =>
            array(
                'id' => 4,
                'dominio_id' => 1,
                'copia_checklist_id' => NULL,
                'versao' => 0,
                'nome' => 'Checklist Todas Ater',
                'instrucoes' => NULL,
                'fl_fluxo_aprovacao' => 0,
                'status' => 'publicado',
                'plano_acao' => 'opcional',
                'formula' => '',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
                'tipo_pontuacao' => 'com_pontuacao',
            ),
            3 =>
            array(
                'id' => 5,
                'dominio_id' => 1,
                'copia_checklist_id' => NULL,
                'versao' => 0,
                'nome' => 'Checklist Ater 2 - Inativo',
                'instrucoes' => NULL,
                'fl_fluxo_aprovacao' => 0,
                'status' => 'inativo',
                'plano_acao' => 'opcional',
                'formula' => '',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
                'tipo_pontuacao' => 'com_pontuacao',
            ),
            4 =>
            array(
                'id' => 6,
                'dominio_id' => 1,
                'copia_checklist_id' => NULL,
                'versao' => 0,
                'nome' => 'Checklist Ater 3 - Rascunho',
                'instrucoes' => NULL,
                'fl_fluxo_aprovacao' => 0,
                'status' => 'publicado',
                'plano_acao' => 'nao_criar',
                'formula' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
                'tipo_pontuacao' => 'com_pontuacao',
            ),
            5 =>
            array(
                'id' => 7,
                'dominio_id' => 2,
                'copia_checklist_id' => NULL,
                'versao' => 0,
                'nome' => 'Checklist PSA',
                'instrucoes' => NULL,
                'fl_fluxo_aprovacao' => 0,
                'status' => 'publicado',
                'plano_acao' => 'opcional',
                'formula' => '',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
                'tipo_pontuacao' => 'com_pontuacao',
            ),
            6 =>
            array(
                'id' => 8,
                'dominio_id' => 1,
                'copia_checklist_id' => NULL,
                'versao' => 0,
                'nome' => 'Cadastro do zero com uma pergunta binaria para testar o policy',
                'instrucoes' => NULL,
                'fl_fluxo_aprovacao' => 0,
                'status' => 'rascunho',
                'plano_acao' => 'nao_criar',
                'formula' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => $createdAt,
                'tipo_pontuacao' => 'com_pontuacao',
            ),
            7 =>
            array(
                'id' => 9,
                'dominio_id' => 1,
                'copia_checklist_id' => NULL,
                'versao' => 0,
                'nome' => 'teste de formulário sem ter status inicial',
                'instrucoes' => '123',
                'fl_fluxo_aprovacao' => 0,
                'status' => 'inativo',
                'plano_acao' => 'nao_criar',
                'formula' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => $createdAt,
                'tipo_pontuacao' => 'com_pontuacao',
            ),
            8 =>
            array(
                'id' => 10,
                'dominio_id' => 1,
                'copia_checklist_id' => NULL,
                'versao' => 0,
                'nome' => 'Teste Editar Formulário enquanto tiver em rascunho',
                'instrucoes' => NULL,
                'fl_fluxo_aprovacao' => 0,
                'status' => 'publicado',
                'plano_acao' => 'opcional',
                'formula' => '',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => $createdAt,
                'tipo_pontuacao' => 'sem_pontuacao',
            ),
        ));

        // \App\Models\Core\ChecklistModel::insert([
        //     ['id' => 1, 'dominio_id' => 1, 'nome' => 'Checklist Semafórica/Binária', 'fl_fluxo_aprovacao' => 0, 'plano_acao' => 'opcional', 'formula' => '', 'tipo_pontuacao' => 'com_pontuacao', 'created_at' => $createdAt, 'updated_at' => $createdAt, 'status' => 'publicado'],
        //     ['id' => 2, 'dominio_id' => 1, 'nome' => 'Checklist Pontuação Simples', 'fl_fluxo_aprovacao' => 1, 'plano_acao' => 'opcional', 'formula' => '', 'tipo_pontuacao' => 'com_pontuacao', 'created_at' => $createdAt, 'updated_at' => $createdAt, 'status' => 'publicado'],
        //     ['id' => 3, 'dominio_id' => 1, 'nome' => 'Checklist Todas Ater', 'fl_fluxo_aprovacao' => 0, 'plano_acao' => 'opcional', 'formula' => '', 'tipo_pontuacao' => 'com_pontuacao', 'created_at' => $createdAt, 'updated_at' => $createdAt, 'status' => 'publicado'],
        //     ['id' => 4, 'dominio_id' => 1, 'nome' => 'Checklist Ater 2 - Inativo', 'fl_fluxo_aprovacao' => 0, 'plano_acao' => 'opcional', 'formula' => '', 'tipo_pontuacao' => 'sem_pontuacao', 'created_at' => $createdAt, 'updated_at' => $createdAt, 'status' => 'inativo'],
        //     ['id' => 5, 'dominio_id' => 1, 'nome' => 'Checklist Ater 3 - Rascunho', 'fl_fluxo_aprovacao' => 0, 'plano_acao' => 'opcional', 'formula' => '', 'tipo_pontuacao' => 'sem_pontuacao', 'created_at' => $createdAt, 'updated_at' => $createdAt, 'status' => 'rascunho'],
        //     ['id' => 6, 'dominio_id' => 2, 'nome' => 'Checklist PSA', 'fl_fluxo_aprovacao' => 0, 'plano_acao' => 'opcional', 'formula' => '', 'tipo_pontuacao' => 'com_pontuacao', 'created_at' => $createdAt, 'updated_at' => $createdAt, 'status' => 'publicado'],
        // ]);

        \DB::table('checklist_dominios')->insert(array(
            0 =>
            array(
                'id' => 1,
                'checklist_id' => 2,
                'dominio_id' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'id' => 2,
                'checklist_id' => 2,
                'dominio_id' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'id' => 3,
                'checklist_id' => 3,
                'dominio_id' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            3 =>
            array(
                'id' => 4,
                'checklist_id' => 4,
                'dominio_id' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            4 =>
            array(
                'id' => 5,
                'checklist_id' => 5,
                'dominio_id' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            5 =>
            array(
                'id' => 6,
                'checklist_id' => 6,
                'dominio_id' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            6 =>
            array(
                'id' => 7,
                'checklist_id' => 7,
                'dominio_id' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            7 =>
            array(
                'id' => 8,
                'checklist_id' => 8,
                'dominio_id' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            8 =>
            array(
                'id' => 9,
                'checklist_id' => 9,
                'dominio_id' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            9 =>
            array(
                'id' => 10,
                'checklist_id' => 10,
                'dominio_id' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
        ));

        // \App\Models\Core\ChecklistDominioModel::insert([
        //     ['checklist_id' => 1, 'dominio_id' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_id' => 1, 'dominio_id' => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_id' => 2, 'dominio_id' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_id' => 3, 'dominio_id' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_id' => 4, 'dominio_id' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_id' => 5, 'dominio_id' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_id' => 6, 'dominio_id' => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt]
        // ]);

        \DB::table('checklist_categorias')->insert(array(
            0 =>
            array(
                'id' => 13,
                'nome' => 'Categoria Semafórica 1',
                'ordem' => 1,
                'checklist_id' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'id' => 14,
                'nome' => 'Categoria Semafórica 2',
                'ordem' => 2,
                'checklist_id' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'id' => 15,
                'nome' => 'Categoria Pontuação Simples com Fluxo',
                'ordem' => 1,
                'checklist_id' => 3,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            3 =>
            array(
                'id' => 16,
                'nome' => 'Categoria Todas Ater 1',
                'ordem' => 1,
                'checklist_id' => 4,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            4 =>
            array(
                'id' => 17,
                'nome' => 'Categoria Todas Ater 2',
                'ordem' => 2,
                'checklist_id' => 4,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            5 =>
            array(
                'id' => 18,
                'nome' => 'Categoria Ater 2 - Inativo',
                'ordem' => 1,
                'checklist_id' => 5,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            6 =>
            array(
                'id' => 19,
                'nome' => 'Categoria Ater 3 - Rascunho',
                'ordem' => 1,
                'checklist_id' => 6,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            7 =>
            array(
                'id' => 20,
                'nome' => 'Categoria PSA',
                'ordem' => 1,
                'checklist_id' => 7,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            8 =>
            array(
                'id' => 21,
                'nome' => 'cat',
                'ordem' => 1,
                'checklist_id' => 8,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            9 =>
            array(
                'id' => 22,
                'nome' => 'teste',
                'ordem' => 1,
                'checklist_id' => 10,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
        ));

        // \App\Models\Core\ChecklistCategoriaModel::insert([
        //     ['id' => 1, 'nome' => 'Categoria Semafórica 1', 'checklist_id' => 1, 'ordem' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 2, 'nome' => 'Categoria Semafórica 2', 'checklist_id' => 1, 'ordem' => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 3, 'nome' => 'Categoria Pontuação Simples com Fluxo', 'checklist_id' => 2, 'ordem' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 4, 'nome' => 'Categoria Todas Ater 1', 'checklist_id' => 3, 'ordem' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 5, 'nome' => 'Categoria Todas Ater 2', 'checklist_id' => 3, 'ordem' => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 6, 'nome' => 'Categoria Ater 2 - Inativo', 'checklist_id' => 4, 'ordem' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 7, 'nome' => 'Categoria Ater 3 - Rascunho', 'checklist_id' => 5, 'ordem' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 8, 'nome' => 'Categoria PSA', 'checklist_id' => 6, 'ordem' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        // ]);

        \DB::table('checklist_perguntas')->insert(array(
            0 =>
            array(
                'id' => 48,
                'checklist_categoria_id' => 13,
                'pergunta_id' => 48,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 1,
                'fl_plano_acao' => 1,
                'plano_acao_prioridade' => 'acao_recomendada',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'id' => 49,
                'checklist_categoria_id' => 13,
                'pergunta_id' => 49,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 1,
                'fl_plano_acao' => 1,
                'plano_acao_prioridade' => 'acao_recomendada',
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'id' => 50,
                'checklist_categoria_id' => 14,
                'pergunta_id' => 50,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 1,
                'fl_plano_acao' => 1,
                'plano_acao_prioridade' => 'priorizacao_tecnica',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            3 =>
            array(
                'id' => 51,
                'checklist_categoria_id' => 14,
                'pergunta_id' => 51,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 1,
                'plano_acao_prioridade' => 'acao_recomendada',
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            4 =>
            array(
                'id' => 52,
                'checklist_categoria_id' => 15,
                'pergunta_id' => 52,
                'peso_pergunta' => 10.0,
                'fl_obrigatorio' => 1,
                'fl_plano_acao' => 1,
                'plano_acao_prioridade' => 'priorizacao_tecnica',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            5 =>
            array(
                'id' => 53,
                'checklist_categoria_id' => 15,
                'pergunta_id' => 59,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 1,
                'plano_acao_prioridade' => 'acao_recomendada',
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            6 =>
            array(
                'id' => 54,
                'checklist_categoria_id' => 16,
                'pergunta_id' => 48,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            7 =>
            array(
                'id' => 55,
                'checklist_categoria_id' => 16,
                'pergunta_id' => 49,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            8 =>
            array(
                'id' => 56,
                'checklist_categoria_id' => 16,
                'pergunta_id' => 50,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 3,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            9 =>
            array(
                'id' => 57,
                'checklist_categoria_id' => 16,
                'pergunta_id' => 51,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 4,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            10 =>
            array(
                'id' => 58,
                'checklist_categoria_id' => 16,
                'pergunta_id' => 52,
                'peso_pergunta' => 10.0,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 5,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            11 =>
            array(
                'id' => 59,
                'checklist_categoria_id' => 16,
                'pergunta_id' => 53,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 6,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            12 =>
            array(
                'id' => 60,
                'checklist_categoria_id' => 16,
                'pergunta_id' => 54,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 7,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            13 =>
            array(
                'id' => 61,
                'checklist_categoria_id' => 17,
                'pergunta_id' => 55,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            14 =>
            array(
                'id' => 62,
                'checklist_categoria_id' => 17,
                'pergunta_id' => 56,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            15 =>
            array(
                'id' => 63,
                'checklist_categoria_id' => 17,
                'pergunta_id' => 57,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 3,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            16 =>
            array(
                'id' => 64,
                'checklist_categoria_id' => 17,
                'pergunta_id' => 58,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 4,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            17 =>
            array(
                'id' => 65,
                'checklist_categoria_id' => 17,
                'pergunta_id' => 59,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 5,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            18 =>
            array(
                'id' => 66,
                'checklist_categoria_id' => 17,
                'pergunta_id' => 60,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 6,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            19 =>
            array(
                'id' => 67,
                'checklist_categoria_id' => 17,
                'pergunta_id' => 61,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 7,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            20 =>
            array(
                'id' => 68,
                'checklist_categoria_id' => 18,
                'pergunta_id' => 62,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 1,
                'plano_acao_prioridade' => 'acao_recomendada',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            21 =>
            array(
                'id' => 69,
                'checklist_categoria_id' => 19,
                'pergunta_id' => 62,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 1,
                'plano_acao_prioridade' => 'acao_recomendada',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            22 =>
            array(
                'id' => 70,
                'checklist_categoria_id' => 20,
                'pergunta_id' => 62,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 0,
                'fl_plano_acao' => 1,
                'plano_acao_prioridade' => 'acao_recomendada',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            23 =>
            array(
                'id' => 71,
                'checklist_categoria_id' => 21,
                'pergunta_id' => 64,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 1,
                'fl_plano_acao' => 1,
                'plano_acao_prioridade' => 'priorizacao_tecnica',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            24 =>
            array(
                'id' => 72,
                'checklist_categoria_id' => 22,
                'pergunta_id' => 48,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 1,
                'fl_plano_acao' => 1,
                'plano_acao_prioridade' => 'priorizacao_tecnica',
                'ordem' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            25 =>
            array(
                'id' => 73,
                'checklist_categoria_id' => 22,
                'pergunta_id' => 50,
                'peso_pergunta' => NULL,
                'fl_obrigatorio' => 1,
                'fl_plano_acao' => 0,
                'plano_acao_prioridade' => NULL,
                'ordem' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
        ));

        // \App\Models\Core\ChecklistPerguntaModel::insert([
        //     //Semafóricas
        //     ['id' => 1, 'checklist_categoria_id' => 1, 'pergunta_id' => 1, 'peso_pergunta' => null, 'ordem' => 1, 'fl_obrigatorio' => 1, 'fl_plano_acao' => 1, 'plano_acao_prioridade' => PlanoAcaoPrioridadeEnum::AcaoRecomendada, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 2, 'checklist_categoria_id' => 1, 'pergunta_id' => 2, 'peso_pergunta' => null, 'ordem' => 2, 'fl_obrigatorio' => 1, 'fl_plano_acao' => 1, 'plano_acao_prioridade' => PlanoAcaoPrioridadeEnum::AcaoRecomendada, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 3, 'checklist_categoria_id' => 2, 'pergunta_id' => 3, 'peso_pergunta' => null, 'ordem' => 1, 'fl_obrigatorio' => 1, 'fl_plano_acao' => 1, 'plano_acao_prioridade' => PlanoAcaoPrioridadeEnum::PriorizacaoTecnica, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 4, 'checklist_categoria_id' => 2, 'pergunta_id' => 4, 'peso_pergunta' => null, 'ordem' => 2, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 1, 'plano_acao_prioridade' => PlanoAcaoPrioridadeEnum::PriorizacaoTecnica, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     //Pontuação Simples com Fluxo Aprovação
        //     ['id' => 5, 'checklist_categoria_id' => 3, 'pergunta_id' => 5, 'peso_pergunta' => 10, 'ordem' => 1, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 6, 'checklist_categoria_id' => 3, 'pergunta_id' => 12, 'peso_pergunta' => null, 'ordem' => 2, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     //Todas Perguntas
        //     ['id' => 7, 'checklist_categoria_id' => 4, 'pergunta_id' => 1, 'peso_pergunta' => null, 'ordem' => 1, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 8, 'checklist_categoria_id' => 4, 'pergunta_id' => 2, 'peso_pergunta' => null, 'ordem' => 2, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 9, 'checklist_categoria_id' => 4, 'pergunta_id' => 3, 'peso_pergunta' => null, 'ordem' => 3, 'fl_obrigatorio' => 1, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 10, 'checklist_categoria_id' => 4, 'pergunta_id' => 4, 'peso_pergunta' => null, 'ordem' => 4, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 11, 'checklist_categoria_id' => 4, 'pergunta_id' => 5, 'peso_pergunta' => 10,    'ordem' => 5, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 12, 'checklist_categoria_id' => 4, 'pergunta_id' => 6, 'peso_pergunta' => null, 'ordem' => 6, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 13, 'checklist_categoria_id' => 4, 'pergunta_id' => 7, 'peso_pergunta' => null, 'ordem' => 7, 'fl_obrigatorio' => 1, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 14, 'checklist_categoria_id' => 5, 'pergunta_id' => 8, 'peso_pergunta' => null, 'ordem' => 1, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 15, 'checklist_categoria_id' => 5, 'pergunta_id' => 9, 'peso_pergunta' => null, 'ordem' => 2, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 16, 'checklist_categoria_id' => 5, 'pergunta_id' => 10, 'peso_pergunta' => null, 'ordem' => 3, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 17, 'checklist_categoria_id' => 5, 'pergunta_id' => 11, 'peso_pergunta' => null, 'ordem' => 4, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 18, 'checklist_categoria_id' => 5, 'pergunta_id' => 12, 'peso_pergunta' => null, 'ordem' => 5, 'fl_obrigatorio' => 1, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 19, 'checklist_categoria_id' => 5, 'pergunta_id' => 13, 'peso_pergunta' => null, 'ordem' => 6, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['id' => 20, 'checklist_categoria_id' => 5, 'pergunta_id' => 14, 'peso_pergunta' => null, 'ordem' => 7, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     //Inativo
        //     ['id' => 21, 'checklist_categoria_id' => 6, 'pergunta_id' => 15, 'peso_pergunta' => null, 'ordem' => 1, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     //Rascunho
        //     ['id' => 22, 'checklist_categoria_id' => 7, 'pergunta_id' => 15, 'peso_pergunta' => null, 'ordem' => 1, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     //PSA
        //     ['id' => 23, 'checklist_categoria_id' => 8, 'pergunta_id' => 15, 'peso_pergunta' => null, 'ordem' => 1, 'fl_obrigatorio' => 0, 'fl_plano_acao' => 0, 'plano_acao_prioridade' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        // ]);

        \DB::table('checklist_aprovacao_users')->insert(array(
            0 =>
            array(
                'id' => 1,
                'checklist_id' => 3,
                'user_id' => 6,
                'created_at' => '2020-05-26 15:33:23',
                'updated_at' => '2020-05-26 15:33:23',
            ),
            1 =>
            array(
                'id' => 2,
                'checklist_id' => 3,
                'user_id' => 10,
                'created_at' => '2020-05-26 15:33:23',
                'updated_at' => '2020-05-26 15:33:23',
            ),
            2 =>
            array(
                'id' => 3,
                'checklist_id' => 3,
                'user_id' => 4,
                'created_at' => '2020-05-26 15:33:23',
                'updated_at' => '2020-05-26 15:33:23',
            ),
            3 =>
            array(
                'id' => 4,
                'checklist_id' => 3,
                'user_id' => 3,
                'created_at' => '2020-05-28 16:06:55',
                'updated_at' => '2020-05-28 16:06:55',
            ),
        ));

        // //Permissão para aprovar um Checklist
        // \App\Models\Core\ChecklistAprovacaoUsersModel::insert([
        //     ['id' => 1, 'checklist_id' => 2, 'user_id' => 6, 'created_at' => $createdAt, 'updated_at' => $createdAt], //Unid. Operacional Zona Norte
        //     ['id' => 2, 'checklist_id' => 2, 'user_id' => 10, 'created_at' => $createdAt, 'updated_at' => $createdAt], //Técnico PSA Edital
        //     ['id' => 3, 'checklist_id' => 2, 'user_id' => 4, 'created_at' => $createdAt, 'updated_at' => $createdAt], //Domínio PSA
        // ]);

        \DB::table('checklist_pergunta_respostas')->insert(array(
            0 =>
            array(
                'id' => 141,
                'checklist_pergunta_id' => 48,
                'resposta_id' => 176,
                'peso' => 5.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'id' => 142,
                'checklist_pergunta_id' => 48,
                'resposta_id' => 177,
                'peso' => 10.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'id' => 143,
                'checklist_pergunta_id' => 48,
                'resposta_id' => 178,
                'peso' => 0.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            3 =>
            array(
                'id' => 144,
                'checklist_pergunta_id' => 54,
                'resposta_id' => 176,
                'peso' => 5.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            4 =>
            array(
                'id' => 145,
                'checklist_pergunta_id' => 54,
                'resposta_id' => 177,
                'peso' => 10.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            5 =>
            array(
                'id' => 146,
                'checklist_pergunta_id' => 54,
                'resposta_id' => 178,
                'peso' => 0.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            6 =>
            array(
                'id' => 147,
                'checklist_pergunta_id' => 49,
                'resposta_id' => 179,
                'peso' => 2.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            7 =>
            array(
                'id' => 148,
                'checklist_pergunta_id' => 49,
                'resposta_id' => 180,
                'peso' => 1.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            8 =>
            array(
                'id' => 149,
                'checklist_pergunta_id' => 49,
                'resposta_id' => 181,
                'peso' => 0.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            9 =>
            array(
                'id' => 150,
                'checklist_pergunta_id' => 49,
                'resposta_id' => 182,
                'peso' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            10 =>
            array(
                'id' => 151,
                'checklist_pergunta_id' => 55,
                'resposta_id' => 179,
                'peso' => 2.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            11 =>
            array(
                'id' => 152,
                'checklist_pergunta_id' => 55,
                'resposta_id' => 180,
                'peso' => 1.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            12 =>
            array(
                'id' => 153,
                'checklist_pergunta_id' => 55,
                'resposta_id' => 181,
                'peso' => 0.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            13 =>
            array(
                'id' => 154,
                'checklist_pergunta_id' => 55,
                'resposta_id' => 182,
                'peso' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            14 =>
            array(
                'id' => 155,
                'checklist_pergunta_id' => 50,
                'resposta_id' => 183,
                'peso' => 10.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            15 =>
            array(
                'id' => 156,
                'checklist_pergunta_id' => 50,
                'resposta_id' => 184,
                'peso' => 0.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            16 =>
            array(
                'id' => 157,
                'checklist_pergunta_id' => 56,
                'resposta_id' => 183,
                'peso' => 10.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            17 =>
            array(
                'id' => 158,
                'checklist_pergunta_id' => 56,
                'resposta_id' => 184,
                'peso' => 0.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            18 =>
            array(
                'id' => 159,
                'checklist_pergunta_id' => 51,
                'resposta_id' => 185,
                'peso' => 10.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            19 =>
            array(
                'id' => 160,
                'checklist_pergunta_id' => 51,
                'resposta_id' => 186,
                'peso' => 0.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            20 =>
            array(
                'id' => 161,
                'checklist_pergunta_id' => 51,
                'resposta_id' => 187,
                'peso' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            21 =>
            array(
                'id' => 162,
                'checklist_pergunta_id' => 57,
                'resposta_id' => 185,
                'peso' => 10.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            22 =>
            array(
                'id' => 163,
                'checklist_pergunta_id' => 57,
                'resposta_id' => 186,
                'peso' => 0.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            23 =>
            array(
                'id' => 164,
                'checklist_pergunta_id' => 57,
                'resposta_id' => 187,
                'peso' => NULL,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            24 =>
            array(
                'id' => 165,
                'checklist_pergunta_id' => 53,
                'resposta_id' => 194,
                'peso' => 10.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            25 =>
            array(
                'id' => 166,
                'checklist_pergunta_id' => 53,
                'resposta_id' => 195,
                'peso' => 5.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            26 =>
            array(
                'id' => 167,
                'checklist_pergunta_id' => 53,
                'resposta_id' => 196,
                'peso' => 1.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            27 =>
            array(
                'id' => 168,
                'checklist_pergunta_id' => 65,
                'resposta_id' => 194,
                'peso' => 10.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            28 =>
            array(
                'id' => 169,
                'checklist_pergunta_id' => 65,
                'resposta_id' => 195,
                'peso' => 5.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            29 =>
            array(
                'id' => 170,
                'checklist_pergunta_id' => 65,
                'resposta_id' => 196,
                'peso' => 1.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            30 =>
            array(
                'id' => 171,
                'checklist_pergunta_id' => 71,
                'resposta_id' => 200,
                'peso' => 2.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            31 =>
            array(
                'id' => 172,
                'checklist_pergunta_id' => 71,
                'resposta_id' => 201,
                'peso' => 0.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            32 =>
            array(
                'id' => 173,
                'checklist_pergunta_id' => 72,
                'resposta_id' => 176,
                'peso' => 12.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            33 =>
            array(
                'id' => 174,
                'checklist_pergunta_id' => 72,
                'resposta_id' => 177,
                'peso' => 6.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            34 =>
            array(
                'id' => 175,
                'checklist_pergunta_id' => 72,
                'resposta_id' => 178,
                'peso' => 0.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            35 =>
            array(
                'id' => 176,
                'checklist_pergunta_id' => 73,
                'resposta_id' => 183,
                'peso' => 10.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
            36 =>
            array(
                'id' => 177,
                'checklist_pergunta_id' => 73,
                'resposta_id' => 184,
                'peso' => 0.0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deleted_at' => NULL,
            ),
        ));

        // //Respostas das perguntas com pontuação
        // \App\Models\Core\ChecklistPerguntaRespostaModel::insert([
        //     //Pergunta Semafórica
        //     ['checklist_pergunta_id' => 1, 'resposta_id' => 1, 'peso' => 10, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 1, 'resposta_id' => 2, 'peso' => 5, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 1, 'resposta_id' => 3, 'peso' => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     ['checklist_pergunta_id' => 7, 'resposta_id' => 1, 'peso' => 10, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 7, 'resposta_id' => 2, 'peso' => 5, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 7, 'resposta_id' => 3, 'peso' => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     //Pergunta Semafórica Cinza
        //     ['checklist_pergunta_id' => 2, 'resposta_id' => 4, 'peso' => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 2, 'resposta_id' => 5, 'peso' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 2, 'resposta_id' => 6, 'peso' => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 2, 'resposta_id' => 7, 'peso' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     ['checklist_pergunta_id' => 8, 'resposta_id' => 4, 'peso' => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 8, 'resposta_id' => 5, 'peso' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 8, 'resposta_id' => 6, 'peso' => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 8, 'resposta_id' => 7, 'peso' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     //Pergunta Binária
        //     ['checklist_pergunta_id' => 3, 'resposta_id' => 8, 'peso' => 10, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 3, 'resposta_id' => 9, 'peso' => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     ['checklist_pergunta_id' => 9, 'resposta_id' => 8, 'peso' => 10, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 9, 'resposta_id' => 9, 'peso' => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     //Pergunta Binária com Cinza
        //     ['checklist_pergunta_id' => 4, 'resposta_id' => 10, 'peso' => 10, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 4, 'resposta_id' => 11, 'peso' => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 4, 'resposta_id' => 12, 'peso' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     ['checklist_pergunta_id' => 10, 'resposta_id' => 10, 'peso' => 10, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 10, 'resposta_id' => 11, 'peso' => 0, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 10, 'resposta_id' => 12, 'peso' => null, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     //Escolha Simples com Pontuação
        //     ['checklist_pergunta_id' => 6, 'resposta_id' => 19, 'peso' => 10, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 6, 'resposta_id' => 20, 'peso' => 5, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 6, 'resposta_id' => 21, 'peso' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],

        //     ['checklist_pergunta_id' => 18, 'resposta_id' => 19, 'peso' => 10, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 18, 'resposta_id' => 20, 'peso' => 5, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        //     ['checklist_pergunta_id' => 18, 'resposta_id' => 21, 'peso' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        // ]);
    }
}
