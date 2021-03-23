<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Class PermissionRoleTableSeeder.
 */

//php artisan permission:cache-reset & php artisan db:seed --class=PermissionRoleTableSeeder
class PermissionRoleTableSeeder extends Seeder
{
    use DisableForeignKeys;
    use TruncateTable;

    private function findOrCreate($roleName)
    {
        try {
            $role = Role::findByName($roleName);
            if ($role)
                return $role;
        } catch (\Exception $e) {
        }

        return Role::create(['name' => $roleName]);
    }

    private function permissionFindOrCreate($roleName)
    {
        try {
            $role = Permission::findByName($roleName);
            if ($role)
                return $role;
        } catch (\Exception $e) {
        }

        return Permission::create(['name' => $roleName]);
    }

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        //P/ recadastrar do zero
        $this->truncateMultiple(['role_has_permissions', 'permissions']);

        // Create Roles
        $superAdminRole     = $this->findOrCreate(config('access.users.admin_role'));
        $adminRole          = $this->findOrCreate(config('access.users.app_admin_role'));
        $domainRole         = $this->findOrCreate(config('access.users.domain_role'));
        $operUnitsRole      = $this->findOrCreate(config('access.users.operational_unit_role'));
        $technicianRole     = $this->findOrCreate(config('access.users.technician_role'));
        $extTechnicianRole  = $this->findOrCreate(config('access.users.ext_technician_role'));

        // Create Permissions
        $permissions[0] = Permission::create(['name' => 'view backend']);

        // Menus
        $permissions[1] = Permission::create(['name' => 'view menu users']);
        $permissions[2] = Permission::create(['name' => 'view menu domains']);
        $permissions[3] = Permission::create(['name' => 'view menu operational units']);
        $permissions[4] = Permission::create(['name' => 'view menu productive units']);
        $permissions[5] = Permission::create(['name' => 'view menu farmers']);

        // Users
        $permissions[6] = Permission::create(['name' => 'view admin users']);
        $permissions[7] = Permission::create(['name' => 'view all domain users']);
        $permissions[8] = Permission::create(['name' => 'view same domain users']);
        $permissions[9] = Permission::create(['name' => 'view same domain operational unit users']);
        $permissions[10] = Permission::create(['name' => 'view same operational unit users']);
        $permissions[11] = Permission::create(['name' => 'view same operational unit technical users']);
        $permissions[12] = Permission::create(['name' => 'view own user']);
        $permissions['user_all_view'] = Permission::create(['name' => 'view all users']);
        $permissions['user_tech_same_domain_view'] = Permission::create(['name' => 'view same domain technical users']);

        $permissions[13] = Permission::create(['name' => 'create admin users']);
        $permissions[14] = Permission::create(['name' => 'create all domain users']);
        $permissions[15] = Permission::create(['name' => 'create same domain users']);
        $permissions[16] = Permission::create(['name' => 'create same domain operational unit users']);
        $permissions[17] = Permission::create(['name' => 'create same operational unit users']);
        $permissions[18] = Permission::create(['name' => 'create same operational unit technical users']);

        $permissions[19] = Permission::create(['name' => 'edit admin users']);
        $permissions[20] = Permission::create(['name' => 'edit all domain users']);
        $permissions[21] = Permission::create(['name' => 'edit same domain users']);
        $permissions[22] = Permission::create(['name' => 'edit same domain operational unit users']);
        $permissions[23] = Permission::create(['name' => 'edit same operational unit users']);
        $permissions[24] = Permission::create(['name' => 'edit same operational unit technical users']);
        $permissions[25] = Permission::create(['name' => 'edit own user']);

        $permissions[26] = Permission::create(['name' => 'delete admin users']);
        $permissions[27] = Permission::create(['name' => 'delete all domain users']);
        $permissions[28] = Permission::create(['name' => 'delete same domain users']);
        $permissions[29] = Permission::create(['name' => 'delete same domain operational unit users']);
        $permissions[30] = Permission::create(['name' => 'delete same operational unit users']);
        $permissions[31] = Permission::create(['name' => 'delete same operational unit technical users']);

        // Domains
        $permissions[32] = Permission::create(['name' => 'view all domains']);
        $permissions[33] = Permission::create(['name' => 'view same domains']);

        $permissions[34] = Permission::create(['name' => 'create domains']);
        $permissions[35] = Permission::create(['name' => 'edit domains']);
        $permissions[36] = Permission::create(['name' => 'delete domains']);

        // Operational Units
        $permissions[37] = Permission::create(['name' => 'view same domain operational units']);
        $permissions[38] = Permission::create(['name' => 'create same domain operational units']);
        $permissions[39] = Permission::create(['name' => 'edit same domain operational units']);
        $permissions[40] = Permission::create(['name' => 'delete same domain operational units']);

        // Productive Units
        $permissions[41] = Permission::create(['name' => 'view same operational units productive units']);
        $permissions[42] = Permission::create(['name' => 'create same operational units productive units']);
        $permissions[43] = Permission::create(['name' => 'edit same operational units productive units']);
        $permissions[44] = Permission::create(['name' => 'delete same operational units productive units']);
        $permissions['pr_unit_domain_view'] = Permission::create(['name' => 'view same domain productive units']);

        // Farmers
        $permissions[45] = Permission::create(['name' => 'view same operational units farmers']);
        $permissions[46] = Permission::create(['name' => 'create same operational units farmers']);
        $permissions[47] = Permission::create(['name' => 'edit same operational units farmers']);
        $permissions[48] = Permission::create(['name' => 'delete same operational units farmers']);
        $permissions['farmer_domain_view'] = Permission::create(['name' => 'view same domain farmers']);

        /**
         * (Keeping increasing the array id in right order)
         */

        // Menus - Continuation
        $permissions[49] = Permission::create(['name' => 'view menu roles']);

        // Operational Units - continuation
        $permissions[50] = Permission::create(['name' => 'view same operational units']);

        // Templates //Caderno de campo
        //$permissions[51] = Permission::create(['name' => 'view menu templates']);
        //$permissions[52] = Permission::create(['name' => 'create templates']);
        //$permissions[53] = Permission::create(['name' => 'edit templates']);
        //$permissions[54] = Permission::create(['name' => 'delete templates']);

        //Caderno de Campo
        $permissions[55] = Permission::create(['name' => 'view menu caderno']);
        $permissions[56] = Permission::create(['name' => 'create caderno']);
        $permissions[57] = Permission::create(['name' => 'edit caderno']);
        $permissions[58] = Permission::create(['name' => 'delete caderno']);

        // Menus - Continuation
        $permissions[59] = Permission::create(['name' => 'view menu regions']);
        $permissions[60] = Permission::create(['name' => 'view menu termos']);

        // Context Menu
        $permissions[61] = Permission::create(['name' => 'attach same operational units productive units']);

        //Checklist Unidade Produtiva
        $permissions['c_up_1'] = Permission::create(['name' => 'view menu checklist_unidade_produtiva']);
        $permissions['c_up_2'] = Permission::create(['name' => 'create checklist_unidade_produtiva']);
        $permissions['c_up_3'] = Permission::create(['name' => 'edit checklist_unidade_produtiva']);
        $permissions['c_up_4'] = Permission::create(['name' => 'delete checklist_unidade_produtiva']);

        //Plano de Ação (Engloba o PDA, PDA Item, Histórico e Histórico Item)
        $permissions['plano_acao_view'] = Permission::create(['name' => 'view menu plano_acao']);
        $permissions['plano_acao_create'] = Permission::create(['name' => 'create plano_acao']);
        $permissions['plano_acao_edit'] = Permission::create(['name' => 'edit plano_acao']);
        $permissions['plano_acao_delete'] = Permission::create(['name' => 'delete plano_acao']);

        $permissions['plano_acao_historico_view'] = Permission::create(['name' => 'view menu plano_acao_historico']);
        $permissions['plano_acao_historico_create'] = Permission::create(['name' => 'create plano_acao_historico']);
        $permissions['plano_acao_historico_edit'] = Permission::create(['name' => 'edit plano_acao_historico']);
        $permissions['plano_acao_historico_delete'] = Permission::create(['name' => 'delete plano_acao_historico']);

        $permissions['plano_acao_item_view'] = Permission::create(['name' => 'view menu plano_acao_item']);
        $permissions['plano_acao_item_create'] = Permission::create(['name' => 'create plano_acao_item']);
        $permissions['plano_acao_item_edit'] = Permission::create(['name' => 'edit plano_acao_item']);
        $permissions['plano_acao_item_delete'] = Permission::create(['name' => 'delete plano_acao_item']);

        $permissions['plano_acao_item_historico_view'] = Permission::create(['name' => 'view menu plano_acao_item_historico']);
        $permissions['plano_acao_item_historico_create'] = Permission::create(['name' => 'create plano_acao_item_historico']);
        $permissions['plano_acao_item_historico_edit'] = Permission::create(['name' => 'edit plano_acao_item_historico']);
        $permissions['plano_acao_item_historico_delete'] = Permission::create(['name' => 'delete plano_acao_item_historico']);


        //Caderno Base
        $permissions['caderno_base_view'] = Permission::create(['name' => 'view menu caderno base']);
        $permissions['caderno_base_create'] = Permission::create(['name' => 'create caderno base']);
        $permissions['caderno_base_edit'] = Permission::create(['name' => 'edit caderno base']);
        $permissions['caderno_base_delete'] = Permission::create(['name' => 'delete caderno base']);

        //Questões (Caderno Base)
        $permissions['questao_caderno_view'] = Permission::create(['name' => 'view menu questao']);
        $permissions['questao_caderno_create'] = Permission::create(['name' => 'create questao']);
        $permissions['questao_caderno_edit'] = Permission::create(['name' => 'edit questao']);
        $permissions['questao_caderno_delete'] = Permission::create(['name' => 'delete questao']);

        //Respostas (Caderno Base)
        $permissions['resposta_caderno_view'] = Permission::create(['name' => 'view menu resposta caderno']);
        $permissions['resposta_caderno_create'] = Permission::create(['name' => 'create resposta caderno']);
        $permissions['resposta_caderno_edit'] = Permission::create(['name' => 'edit resposta caderno']);
        $permissions['resposta_caderno_delete'] = Permission::create(['name' => 'delete resposta caderno']);

        //Checklist Base
        $permissions['checklist_base_view'] = Permission::create(['name' => 'view menu checklist base']);
        $permissions['checklist_base_create'] = Permission::create(['name' => 'create checklist base']);
        $permissions['checklist_base_edit'] = Permission::create(['name' => 'edit checklist base']);
        $permissions['checklist_base_delete'] = Permission::create(['name' => 'delete checklist base']);
        $permissions['checklist_base_duplicate'] = Permission::create(['name' => 'duplicate checklist base']);

        //Categorias (Checklist Base)
        $permissions['categoria_checklist_view'] = Permission::create(['name' => 'view menu categoria checklist']);
        $permissions['categoria_checklist_create'] = Permission::create(['name' => 'create categoria checklist']);
        $permissions['categoria_checklist_edit'] = Permission::create(['name' => 'edit categoria checklist']);
        $permissions['categoria_checklist_delete'] = Permission::create(['name' => 'delete categoria checklist']);

        //Perguntas (Checklist Base)
        $permissions['pergunta_checklist_view'] = Permission::create(['name' => 'view menu pergunta checklist']);
        $permissions['pergunta_checklist_create'] = Permission::create(['name' => 'create pergunta checklist']);
        $permissions['pergunta_checklist_edit'] = Permission::create(['name' => 'edit pergunta checklist']);
        $permissions['pergunta_checklist_delete'] = Permission::create(['name' => 'delete pergunta checklist']);

        //Respostas (Checklist Base)
        $permissions['resposta_checklist_view'] = Permission::create(['name' => 'view menu resposta checklist']);
        $permissions['resposta_checklist_create'] = Permission::create(['name' => 'create resposta checklist']);
        $permissions['resposta_checklist_edit'] = Permission::create(['name' => 'edit resposta checklist']);
        $permissions['resposta_checklist_delete'] = Permission::create(['name' => 'delete resposta checklist']);

        //Fluxo de moderação (Checklist)
        $permissions['fluxo_checklist_view'] = Permission::create(['name' => 'view menu fluxo checklist']);
        $permissions['fluxo_checklist_mod'] = Permission::create(['name' => 'moderate fluxo checklist']);

        // Syncing permissions into Roles
        $adminRole->syncPermissions([
            $permissions[0], $permissions[1], $permissions[2], $permissions[3],
            $permissions[4], $permissions[5], $permissions[6], $permissions[7],
            $permissions[13], $permissions[14], $permissions[19], $permissions[20],
            $permissions[26], $permissions[27], $permissions[32], $permissions[34],
            $permissions[35], $permissions[36], $permissions[55], $permissions[59],
            $permissions[60], $permissions['c_up_1'], $permissions['plano_acao_view'],
            $permissions['plano_acao_historico_view'], $permissions['plano_acao_item_view'], $permissions['plano_acao_item_historico_view'],
            $permissions['caderno_base_view'], $permissions['questao_caderno_view'],
            $permissions['checklist_base_view'], $permissions['categoria_checklist_view'],
            $permissions['pergunta_checklist_view'], $permissions['resposta_caderno_view'],
            $permissions['resposta_checklist_view'], $permissions['user_all_view']
        ]);

        $domainRole->syncPermissions([
            $permissions[0], $permissions[1], $permissions[2], $permissions[3], $permissions[4],
            $permissions[5], $permissions[8], $permissions[9], $permissions[15],
            $permissions[16], $permissions[21], $permissions[22], $permissions[28],
            $permissions[29], $permissions[33], $permissions[37], $permissions[38],
            $permissions[39], $permissions[40], $permissions[55], $permissions[59],
            $permissions[61],
            $permissions['c_up_1'], $permissions['c_up_4'], $permissions['plano_acao_view'], $permissions['caderno_base_view'],
            $permissions['plano_acao_historico_view'], $permissions['plano_acao_item_view'], $permissions['plano_acao_item_historico_view'],
            $permissions['plano_acao_delete'],
            $permissions['caderno_base_create'], $permissions['caderno_base_edit'],
            $permissions['caderno_base_delete'], $permissions['questao_caderno_view'],
            $permissions['questao_caderno_create'], $permissions['questao_caderno_edit'],
            $permissions['questao_caderno_delete'], $permissions['checklist_base_view'],
            $permissions['checklist_base_create'], $permissions['checklist_base_edit'],
            $permissions['checklist_base_delete'], $permissions['categoria_checklist_view'],
            $permissions['categoria_checklist_create'], $permissions['categoria_checklist_edit'],
            $permissions['categoria_checklist_delete'], $permissions['pergunta_checklist_view'],
            $permissions['pergunta_checklist_create'], $permissions['pergunta_checklist_edit'],
            $permissions['pergunta_checklist_delete'], $permissions['resposta_caderno_view'],
            $permissions['resposta_caderno_create'], $permissions['resposta_caderno_edit'],
            $permissions['resposta_caderno_delete'], $permissions['resposta_checklist_view'],
            $permissions['resposta_checklist_create'], $permissions['resposta_checklist_edit'],
            $permissions['resposta_checklist_delete'], $permissions['pr_unit_domain_view'],
            $permissions['farmer_domain_view'], $permissions['user_tech_same_domain_view'],
            $permissions['checklist_base_duplicate']
        ]);

        $operUnitsRole->syncPermissions([
            $permissions[0], $permissions[1], $permissions[3], $permissions[4], $permissions[5],
            $permissions[10], $permissions[11], $permissions[17], $permissions[18],
            $permissions[23], $permissions[24], $permissions[30], $permissions[31],
            $permissions[41], $permissions[42], $permissions[43], $permissions[44],
            $permissions[45], $permissions[46], $permissions[47], $permissions[48],
            $permissions[50], $permissions[55], $permissions[56], $permissions[57],
            $permissions['c_up_1'], $permissions['c_up_2'],
            $permissions['c_up_3'], $permissions['c_up_4'], $permissions['plano_acao_view'],
            $permissions['plano_acao_create'], $permissions['plano_acao_edit'], $permissions['plano_acao_delete'],
            $permissions['plano_acao_historico_view'], $permissions['plano_acao_item_view'], $permissions['plano_acao_item_historico_view'],
            $permissions['plano_acao_historico_create'],
            $permissions['plano_acao_item_create'], $permissions['plano_acao_item_edit'], $permissions['plano_acao_item_delete'],
            $permissions['plano_acao_item_historico_create'],
            $permissions['caderno_base_view'], $permissions['questao_caderno_view'],
            $permissions['checklist_base_view'], $permissions['categoria_checklist_view'],
            $permissions['pergunta_checklist_view'], $permissions['resposta_caderno_view'],
            $permissions['resposta_checklist_view']
        ]);

        $technicianRole->syncPermissions([
            $permissions[0], $permissions[1], $permissions[3], $permissions[4], $permissions[5],
            $permissions[12], $permissions[25], $permissions[41], $permissions[42],
            $permissions[43], $permissions[44], $permissions[45], $permissions[46],
            $permissions[47], $permissions[48], $permissions[50], $permissions[55],
            $permissions[56], $permissions[57], $permissions['c_up_1'], $permissions['c_up_2'],
            $permissions['c_up_3'], $permissions['c_up_4'], $permissions['plano_acao_view'],
            $permissions['plano_acao_create'], $permissions['plano_acao_edit'], $permissions['plano_acao_delete'],
            $permissions['plano_acao_historico_view'], $permissions['plano_acao_item_view'], $permissions['plano_acao_item_historico_view'],
            $permissions['plano_acao_historico_create'],
            $permissions['plano_acao_item_create'], $permissions['plano_acao_item_edit'], $permissions['plano_acao_item_delete'],
            $permissions['plano_acao_item_historico_create'],
            $permissions['caderno_base_view'], $permissions['questao_caderno_view'],
            $permissions['checklist_base_view'], $permissions['categoria_checklist_view'],
            $permissions['pergunta_checklist_view'], $permissions['resposta_caderno_view'],
            $permissions['resposta_checklist_view']
        ]);

        $extTechnicianRole->syncPermissions([
            $permissions[0], $permissions[1], $permissions[4], $permissions[5],
            $permissions[12]
        ]);


        $permissionReport = $this->permissionFindOrCreate('view menu report');

        $permissionReport->assignRole(Role::findByName(config('access.users.app_admin_role')));
        $permissionReport->assignRole(Role::findByName(config('access.users.domain_role')));
        $permissionReport->assignRole(Role::findByName(config('access.users.operational_unit_role')));
        $permissionReport->assignRole(Role::findByName(config('access.users.technician_role')));
        $permissionReport->assignRole(Role::findByName(config('access.users.ext_technician_role')));

        $permissionReportRestricted = $this->permissionFindOrCreate('report restricted');

        DB::table('permissions')
            ->whereIn('id', [$permissionReportRestricted->id])
            ->update([
                'fl_domain_user' => 1,
            ]);

        $this->enableForeignKeys();
    }
}
