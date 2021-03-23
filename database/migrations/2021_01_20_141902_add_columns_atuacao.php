<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsAtuacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produtores', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('unidade_produtivas', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('checklist_unidade_produtivas', function (Blueprint $table) {
            $table->unsignedBigInteger('finish_user_id')->nullable();
            $table->foreign('finish_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamp('finished_at', 0)->nullable();
        });

        Schema::table('cadernos', function (Blueprint $table) {
            $table->unsignedBigInteger('finish_user_id')->nullable();
            $table->foreign('finish_user_id')->references('id')->on('users')->onDelete('cascade');
        });

        \DB::table('unidade_produtivas')
            ->whereNull('user_id')
            ->update(array('user_id' => 5));

        \DB::table('produtores')
            ->whereNull('user_id')
            ->update(array('user_id' => 5));

        \DB::table('checklist_unidade_produtivas')
            ->whereNull('finish_user_id')
            ->where('status', 'finalizado')
            ->update(array('finish_user_id' => DB::raw("`user_id`"), 'finished_at' => DB::raw("`updated_at`")));

        \DB::table('cadernos')
            ->whereNull('finish_user_id')
            ->where('status', 'finalizado')
            ->update(array('finish_user_id' => DB::raw("`user_id`"), 'finished_at' => DB::raw("`updated_at`")));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produtores', function (Blueprint $table) {
            $table->dropForeign('produtores_user_id_foreign');
            $table->dropColumn(['user_id']);
        });

        Schema::table('unidade_produtivas', function (Blueprint $table) {
            $table->dropForeign('unidade_produtivas_user_id_foreign');
            $table->dropColumn(['user_id']);
        });

        Schema::table('checklist_unidade_produtivas', function (Blueprint $table) {
            $table->dropForeign('checklist_unidade_produtivas_finish_user_id_foreign');
            $table->dropColumn(['finish_user_id', 'finished_at']);
        });

        Schema::table('cadernos', function (Blueprint $table) {
            $table->dropForeign('cadernos_finish_user_id_foreign');
            $table->dropColumn(['finish_user_id']);
        });
    }
}
