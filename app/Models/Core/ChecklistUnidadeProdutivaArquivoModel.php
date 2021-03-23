<?php

namespace App\Models\Core;

use App\Models\Core\Traits\ImportFillableCreatedAt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Utilizado pelo Formulário aplicado (Arquivos)
 */
class ChecklistUnidadeProdutivaArquivoModel extends Model
{
    use SoftDeletes;
    use ImportFillableCreatedAt;

    public $incrementing = false;

    protected $table = 'checklist_unidade_produtiva_arquivos';

    protected $fillable = ['id', 'checklist_unidade_produtiva_id', 'tipo', 'nome', 'descricao', 'lat', 'lng', 'arquivo', 'deleted_at'];

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        //Método para o "id" único do tipo string, consumido pelo APP (sync)
        self::creating(function ($model) {
            if ($model->id)
                return;

            $model->id = Uuid::generate(4)->string;
        });
    }

    public function getUrlAttribute()
    {
        if (!$this->arquivo) {
            return null;
        }

        return \Storage::url('/') . $this->arquivo;
    }
}
