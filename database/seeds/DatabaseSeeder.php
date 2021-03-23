<?php

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use TruncateTable;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        //Não executa os seeds no .env de produção. Faz um doublecheck
        if (App::environment('production')) {
            $countUsers = User::count();

            if ($countUsers != 0) {
                die("Não é possível rodar em 'production' porque já existem usuários cadastrados no sistema.");
            }
        }

        Model::unguard();

        // Comentado p/ evitar truncante em tabelas importantes
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // $this->truncateMultiple([
        //     'cache',
        //     'failed_jobs',
        //     'ledgers',
        //     'jobs',
        //     'sessions',

        //     //UsuariosSeeder
        //     'user_dominios',
        //     'user_unidade_operacionais',

        //     //ProdutoresSeeder
        //     'unidade_operacional_unidade_produtiva',
        //     'produtor_unidade_produtiva',
        //     'produtores',
        //     'unidade_produtivas',

        //     //DominioSeeder
        //     'unidade_operacionais',
        //     'dominios',

        //     //Dados Gerais
        //     'etinias',
        //     'assistencia_tecnica_tipos',
        //     'generos',
        //     'relacoes',
        //     'instalacao_tipos',
        //     'instalacoes',
        //     'solo_categorias',
        //     'tipo_posses',
        //     'canal_comercializacoes',
        //     'risco_contaminacao_aguas',
        //     'tipo_fonte_aguas',
        //     'outorgas',
        //     'dedicacoes',
        //     'pressao_sociais',
        //     'certificacoes',
        //     'termos_de_usos',

        //     //CadernoCampoSeeder
        //     'template_pergunta_templates',
        //     'template_perguntas',
        //     'template_respostas',
        //     'templates',
        //     'cadernos',
        //     'caderno_resposta_caderno',
        //     'caderno_arquivos',

        //     //ChecklistSeeder
        //     'perguntas',
        //     'respostas',
        //     'checklists',
        //     'checklist_perguntas',
        //     'checklist_dominios',
        //     'checklist_pergunta_respostas',
        //     'checklist_unidade_operacionais',
        //     'checklist_unidade_produtivas',
        //     'checklist_snapshot_respostas',
        //     'checklist_users',
        //     'checklist_categorias',
        //     'checklist_aprovacao_logs',
        //     'checklist_aprovacao_users',
        //     'unidade_produtiva_respostas',

        //     //PDA
        //     'plano_acoes',
        //     'plano_acao_historicos',
        //     'plano_acao_itens',
        //     'plano_acao_item_historicos',

        //     //Produtores/Unidades Produtivas
        //     'grau_instrucoes',
        //     'rendimento_comercializacoes',
        //     'renda_agriculturas',
        //     'esgotamento_sanitarios',
        //     'residuo_solidos',

        //     'dados',
        //     'dado_abrangencia_cidades',
        //     'dado_abrangencia_estados',
        //     'dado_abrangencia_regioes',
        //     'dado_unidade_produtivas'
        // ]);

        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            AuthTableSeeder::class,
            EstadosTableSeederLatLng::class,
            DominiosSeeder::class,
            DadosGeraisSeeder::class,
            UsuariosSeeder::class,
            CadernoCampoSeeder::class,
            ProdutoresSeeder::class,
            CadernoCampoCargaSeeder::class,
            ChecklistSeeder::class,
            // ChecklistSeederBase::class, //Apenas para homologação
            ChecklistCargaSeeder::class,
            SyncMvp1Seeder::class,
            SyncMvp2Seeder::class,
            DadosSampaRuralSeeder::class,
            SyncMvp4Seeder::class,
        ]);

        Model::reguard();
    }
}
