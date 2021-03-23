<?php

use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SeedReportPermissions extends Migration
{
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
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->boolean('fl_domain_user')->nullable()->default(false);
        });

        try {
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
        } catch(\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('fl_domain_user')->nullable()->default(false);
        });
    }
}
