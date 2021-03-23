<?php

namespace App\Models\Core;

use App\Models\Core\Traits\ImportFillableCreatedAt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Uuid;

/**
 * Utilizado pelo Caderno (Arquivos do caderno de campo)
 */
class CadernoArquivoModel extends Model
{
    use SoftDeletes;
    use ImportFillableCreatedAt;

    public $incrementing = false;

    protected $table = 'caderno_arquivos';

    protected $fillable = ['id', 'caderno_id', 'nome', 'arquivo', 'tipo', 'lat', 'lng', 'descricao', 'deleted_at'];

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
