<?php

namespace App\Models\Core;

use App\Helpers\General\AppHelper;
use App\Models\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;
use App\Models\Auth\User;

class PlanoAcaoItemHistoricoModel extends Model
{
    use SoftDeletes;
    use DateFormat;

    public $incrementing = false;

    protected $table = 'plano_acao_item_historicos';

    protected $fillable = ['id', 'plano_acao_item_id', 'user_id', 'texto', 'deleted_at'];

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if ($model->id)
                return;

            //Método para o "id" único do tipo string, consumido pelo APP (sync)
            $model->id = (string) Uuid::generate(4);
        });

        self::created(function ($model) {
            if (\Config::get('app_sync')) {
                return;
            }

            /**
             * Ao criar pela primeira vez o registro
             *
             * Atualiza o campo "ultima_observacao" / "ultima_observacao_data" do Plano de Ação
             */
            $planoAcaoItemModel = PlanoAcaoItemModel::where("id", $model->plano_acao_item_id)->first();
            $planoAcaoItemModel->ultima_observacao = $model->texto;
            $planoAcaoItemModel->ultima_observacao_data = $model->updated_at;
            $planoAcaoItemModel->save();
        });
    }

    /**
     * Retorna o usuário que criou o históricoo
     *
     *  Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes()->withTrashed();
    }

    /**
     * Utilizado no Report p/ retornar data + texto
     *
     * @return string
     */
    public function getTextoReportAttribute()
    {
        return AppHelper::formatDate($this->created_at, 'd/m/Y') . ' - ' . $this->texto;
    }
}
