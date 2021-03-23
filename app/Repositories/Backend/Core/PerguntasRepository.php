<?php

namespace App\Repositories\Backend\Core;

use App\Enums\TipoPerguntaEnum;
use App\Exceptions\GeneralException;
use App\Models\Core\PerguntaModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PerguntasRepository extends BaseRepository
{
    public function __construct(PerguntaModel $model)
    {
        $this->model = $model;
    }

    /**
     * Valida se o total de respostas cadastras fecha com o tipo de pergunta
     *
     * @param  PerguntaModel $model
     * @return bool
     */
    private function isValidRespostas(PerguntaModel $model): bool
    {
        $tipo_pergunta = $model->tipo_pergunta;

        if (in_array($tipo_pergunta, [TipoPerguntaEnum::Semaforica, TipoPerguntaEnum::SemaforicaCinza, TipoPerguntaEnum::Binaria, TipoPerguntaEnum::BinariaCinza])) {
            $total = count($model->respostas);
            if ($tipo_pergunta == TipoPerguntaEnum::Semaforica && $total == 3) {
                return true;
            } else if ($tipo_pergunta == TipoPerguntaEnum::SemaforicaCinza && $total == 4) {
                return true;
            } else if ($tipo_pergunta == TipoPerguntaEnum::Binaria && $total == 2) {
                return true;
            } else if ($tipo_pergunta == TipoPerguntaEnum::BinariaCinza && $total == 3) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Cria uma pergunta base para ser utilizada no template do formulário
     *
     * @param  mixed $data
     * @return PerguntaModel
     */
    public function create(array $data): PerguntaModel
    {
        return DB::transaction(function () use ($data) {
            $model = $this->model::create($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualiza uma pergunta
     *
     * @param  PerguntaModel $model
     * @param  mixed $data
     * @return void
     */
    public function update(PerguntaModel $model, array $data): PerguntaModel
    {
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);

            if (!$this->isValidRespostas($model)) {
                throw new GeneralException('Você precisa cadastrar todas as alternativas de resposta.');
            }

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remove a pergunta
     *
     * - Perguntas atreladas em algum Checklist não podem ser excluidas, existe um tratamento via Policy
     *
     * @param  mixed $model
     * @return bool
     */
    public function delete(PerguntaModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
