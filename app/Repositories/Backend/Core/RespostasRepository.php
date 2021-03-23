<?php

namespace App\Repositories\Backend\Core;

use App\Enums\TipoPerguntaEnum;
use App\Exceptions\GeneralException;
use App\Models\Core\PerguntaModel;
use App\Models\Core\RespostaModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class RespostasRepository extends BaseRepository
{
    public function __construct(RespostaModel $model)
    {
        $this->model = $model;
    }

    /**
     * Cria uma resposta para uma pergunta
     *
     * @param  mixed $data
     * @return RespostaModel
     */
    public function create(array $data): RespostaModel
    {
        return DB::transaction(function () use ($data) {
            //Só faz essa validação para perguntas que tenham cor (Tipo Semafóricas/Binária)
            if (@$data['cor']) {
                if (RespostaModel::where("pergunta_id", $data['pergunta_id'])->where("cor", @$data['cor'])->exists()) {
                    if (PerguntaModel::where("id", $data['pergunta_id'])->first()->tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza) {
                        throw new GeneralException("Só pode haver uma resposta na cor selecionada ('não se aplica').");
                    } else {
                        throw new GeneralException("Esta cor já foi utilizada.");
                    }
                }
            }

            $model = $this->model::create($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualização de uma resposta
     *
     * @param  RespostaModel $model
     * @param  mixed $data
     * @return RespostaModel
     */
    public function update(RespostaModel $model, array $data): RespostaModel
    {
        return DB::transaction(function () use ($model, $data) {
            if (@$data['cor']) {
                if (RespostaModel::where('pergunta_id', $model->pergunta_id)->where("cor", @$data['cor'])->where("id", '!=', $model->id)->exists()) {
                    if (PerguntaModel::where("id", $model->pergunta_id)->first()->tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza) {
                        throw new GeneralException("Só pode haver uma resposta 'não se aplica'.");
                    } else {
                        throw new GeneralException("Esta cor já foi utilizada.");
                    }
                }
            }

            $model->update($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remoção de uma resposta
     *
     * @param  RespostaModel $model
     * @return bool
     */
    public function delete(RespostaModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            try {
                if ($model->forceDelete()) {
                    return true;
                }
            } catch (\Exception $e) {
                throw new GeneralException('Esta resposta já foi utilizada em algum formulário aplicado. Ela não pode ser removida.');
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
