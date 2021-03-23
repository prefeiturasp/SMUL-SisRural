<?php

namespace App\Models\Core;

use App\Models\Core\Traits\ImportFillableCreatedAt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Utilizado pela Unidade Produtiva (UnidadeProdutivaModel)
 *
 * É o bloco "Pessoas" dentro da "Unidade Produtiva
 */
class ColaboradorModel extends Model
{
    use SoftDeletes;
    use ImportFillableCreatedAt;

    public $incrementing = false;

    protected $table = 'colaboradores';

    protected $fillable = ['id', 'nome', 'cpf', 'funcao', 'dedicacao_id', 'relacao_id', 'unidade_produtiva_id', 'deleted_at'];

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

        self::saved(function ($model) {
            /*
            * Ao salvar o colaborador, atualiza a coluna "socios" da Unidade Produtiva
            * (essa coluna é utilizado nas buscas e em várias partes do sistema, foi feito a
            * replicação p/ facilitar sua utilização no CMS e no APP)
            */
            $names =  $model->unidadeProdutiva->colaboradores()->whereHas('relacao', function ($q) {
                $q->where('relacao_id', 1); //Sócio
                $q->orWhere('relacao_id', 3); //Coproprietário
            })->orderBy('nome')->pluck('nome')->toArray();

            $names = join(", ", $names);
            $unidProdutiva = $model->unidadeProdutiva;
            $unidProdutiva->socios = $names;
            $unidProdutiva->save();
        });
    }

    public function unidadeProdutiva()
    {
        return $this->belongsTo(UnidadeProdutivaModel::class, 'unidade_produtiva_id');
    }

    public function relacao()
    {
        return $this->belongsTo(RelacaoModel::class, 'relacao_id');
    }

    public function dedicacao()
    {
        return $this->belongsTo(DedicacaoModel::class, 'dedicacao_id');
    }
}
