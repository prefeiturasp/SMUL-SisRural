<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Arquivos das Unidades Produtivas
 */
class UnidadeProdutivaArquivoModel extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'unidade_produtiva_arquivos';

    protected $fillable = ['id', 'unidade_produtiva_id', 'nome', 'arquivo', 'tipo', 'lat', 'lng', 'descricao', 'deleted_at'];

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if ($model->id)
                return;

            $model->id = Uuid::generate(4)->string;
        });
    }

    public function unidadeProdutiva()
    {
        return $this->belongsTo(UnidadeProdutivaModel::class, 'unidade_produtiva_id');
    }

    public function getUrlAttribute()
    {
        if (!$this->arquivo) {
            return null;
        }

        return \Storage::url('/') . $this->arquivo;
    }
}
