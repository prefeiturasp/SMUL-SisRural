<?php

namespace App\Models\Core;

use App\Models\Core\Traits\ImportFillableCreatedAt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Utilizado pela Unidade Produtiva (UnidadeProdutivaModel)
 */
class InstalacaoModel extends Model
{
    use SoftDeletes;
    use ImportFillableCreatedAt;

    public $incrementing = false;

    protected $table = 'instalacoes';

    protected $fillable = ['id', 'descricao', 'quantidade', 'area', 'observacao', 'localizacao', 'unidade_produtiva_id', 'instalacao_tipo_id', 'deleted_at'];

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
    }

    public function unidadeProdutiva()
    {
        return $this->belongsTo(UnidadeProdutivaModel::class, 'unidade_produtiva_id');
    }

    public function instalacaoTipo()
    {
        return $this->belongsTo(InstalacaoTipoModel::class, 'instalacao_tipo_id');
    }
}
