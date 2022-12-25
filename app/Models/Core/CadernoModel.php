<?php

namespace App\Models\Core;

use App\Enums\CadernoStatusEnum;
use App\Enums\TipoTemplatePerguntaEnum;
use App\Models\Core\Traits\Scope\CadernoPermissionScope;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Auth\User;
use App\Models\Core\Traits\Attribute\RolesAppAttribute;
use App\Models\Core\Traits\ImportFillableCreatedAt;
use App\Models\Traits\DateFormat;
use Carbon\Carbon;
use Uuid;

/**
 * Base da estrutura do Caderno de Campo
 */
class CadernoModel extends Model
{
    use SoftDeletes;
    use DateFormat;
    use RolesAppAttribute;
    use ImportFillableCreatedAt;

    public $incrementing = false;

    protected $table = 'cadernos';

    protected $fillable = ['id', 'template_id', 'produtor_id', 'unidade_produtiva_id', 'status', 'user_id', 'protocolo', 'finished_at', 'deleted_at', 'finish_user_id'];

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CadernoPermissionScope);

        self::creating(function ($model) {
            /**
             * Caso o Caderno seja "finalizado", atualiza a data (finished_at) que foi finalizado
             */
            if ($model->status == CadernoStatusEnum::Finalizado) {
                if (!$model->finished_at) { //Caso seja uma importação, o "finished_at" já vai vir preenchido
                    $model->finished_at = Carbon::now();
                }

                if (!$model->finish_user_id && \Auth::user()) {
                    $model->finish_user_id = \Auth::user()->id;
                }
            }


            if ($model->id)
                return;

            //Método para o "id" único do tipo string, consumido pelo APP (sync)
            $model->id = (string) Uuid::generate(4);
        });

        /*
        * Caso o status do caderno é alterado de XXX p/ Finalizado, atualiza a data (finished_at) que foi finalizado
        */
        self::updating(function ($model) {
            if ($model->isDirty('status') && $model->status == CadernoStatusEnum::Finalizado) {
                $model->finished_at = Carbon::now();
                $model->finish_user_id = \Auth::user() ? \Auth::user()->id : null; //Seeder
            }
        });

        /**
         * No momento da criação, gera o protocolo
         */
        self::created(function ($model) {
            //$model = CadernoModel::findOrFail($model->id); //Redundância porque o ID não estava retornando no $model
            $model->protocolo = self::getProtocolo($model, \DB::getPdo()->lastInsertId());
            $model->timestamps = false;
            $model->save();
        });
    }

    /**
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     *
     * - "withTrashed" p/ mostrar o template utilizado -> admin/cadernos e @id_caderno@/view
     * - "withoutGlobalScope" p/ permitir o acesso do template independente da permissão do usuário (utilizado para listagem/visualização)
     *
     * @return mixed
     */
    public function template()
    {
        return $this->belongsTo(TemplateModel::class)->withoutGlobalScopes()->withTrashed();
    }

    /**
     * Essa Relation considera o escopo do model
     *
     * - "withTrashed" p/ mostrar o template utilizado -> admin/cadernos e @id_caderno@/view
     *
     * @return mixed
     */
    public function templateScoped()
    {
        return $this->belongsTo(TemplateModel::class, 'template_id')->withTrashed();
    }

    /**
     * Retorna o produtor vinculado ao caderno de campo
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     *
     * @return mixed
     */
    public function produtor()
    {
        return $this->belongsTo(ProdutorModel::class)->withoutGlobalScopes();
    }

    /**
     * Retorna a unidade produtiva vinculada ao caderno de campo
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     *
     * @return mixed
     */
    public function unidadeProdutiva()
    {
        return $this->belongsTo(UnidadeProdutivaModel::class)->withoutGlobalScopes();
    }

    /**
     * Retorna as respostas que o caderno de campo possuí
     * @return mixed
     */
    public function respostasMany()
    {
        return $this->hasMany(CadernoRespostaCadernoModel::class, 'caderno_id');
    }

    /**
     * Retorna os arquivos que o caderno de campo possuí
     * @return mixed
     */
    public function arquivos()
    {
        return $this->hasMany(CadernoArquivoModel::class, 'caderno_id');
    }

    /**
     * Retorna o usuário que criou o caderno de campo
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     *
     * @return mixed
     */
    public function usuarioFinish()
    {
        return $this->belongsTo(User::class, 'finish_user_id')->withoutGlobalScopes();
    }

    /**
     * Retorna o usuário que finalizou o caderno de campo
     *
     * Essa Relation está desconsiderando o escopo do model para permitir a visualização por parte de usuários
     * que não teriam essa permissão via scope da model relacionada
     *
     * @return mixed
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes()->withTrashed();
    }

    /**
     * Retorna as respostas p/ serem consumidos pelo APP
     *
     * Métodos "offline" utilizados p/ o download de dados do APP
     *
     * @return mixed
     */
    public function respostasManyOffline()
    {
        return $this->hasMany(CadernoRespostaCadernoModel::class, 'caderno_id')->withTrashed();
    }

    /**
     * Retorna os arquivos p/ serem consumidos pelo APP
     *
     * Métodos "offline" utilizados p/ o download de dados do APP
     *
     * @return mixed
     */
    public function arquivosManyOffline()
    {
        return $this->hasMany(CadernoArquivoModel::class, 'caderno_id')->withTrashed();
    }

    /**
     * Retorna o protocolo formatado para ser inserido na coluna "protocolo"
     *
     * @param mixed $data (Model do Caderno)
     * @return string
     */
    private static function getProtocolo($data, $uid)
    {
        $date = Carbon::parse($data->created_at)->format('Ymd');

        $uidUnidadeProdutiva = $data->unidadeProdutiva ? $data->unidadeProdutiva->uid : '_';
        $uidProdutor = $data->produtor ? $data->produtor->uid : '_';

        $number = $date . $uid . $data->template_id . $uidUnidadeProdutiva . $uidProdutor;

        return $number;
    }

    /**
     * Fix p/ retornar a unidade produtiva, no sorting do DataTable, utilizado no CadernoController
     *
     * @return mixed
     */
    public function datatable_unidade_produtiva()
    {
        return $this->unidadeProdutiva();
    }

    /**
     * Métodos escopados, para não bypassar o permissionScope
     *
     * Eles são utilizados dentro do PermissionScope
     *
     * @return mixed
     */
    public function unidadeProdutivaScoped()
    {
        return $this->belongsTo(UnidadeProdutivaModel::class, 'unidade_produtiva_id');
    }

    public function getPerguntasRespostas()
    {
        $perguntas = $this->template->perguntasWithTrashed;
        $respostas = $this->respostasMany()->with('templateResposta')->get()->toArray();

        foreach ($perguntas as $k => &$pergunta) {
            $resposta = @array_values(array_filter($respostas, function ($resposta) use ($pergunta) {
                return $resposta['template_pergunta_id'] === $pergunta['id'];
            }));

            $value = [];

            if ($pergunta['tipo'] == TipoTemplatePerguntaEnum::Check || $pergunta['tipo'] == TipoTemplatePerguntaEnum::MultipleCheck) {
                foreach ($resposta as $kk => $vv) {
                    $value[] = $vv['template_resposta']['descricao'];
                }
            } else {
                $value = [@$resposta[0]['resposta']];
            }

            $pergunta['resposta'] = join(', ', $value);
        }

        return $perguntas;
    }

    /**
     * Relação many to many com usuários (técnicos que participaram da visita em campo)
     *
     *
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
