<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\TemplatePerguntaModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;


class TemplatePerguntasRepository extends BaseRepository
{

    public function __construct(TemplatePerguntaModel $model)
    {
        $this->model = $model;
    }

    /**
     * Criar uma pergunta (caderno de campo)
     *
     * @param  mixed $data
     * @return TemplatePerguntaModel
     */
    public function create(array $data): TemplatePerguntaModel
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
     * Atualiza uma pergunta (caderno de campo)
     *
     * @param  TemplatePerguntaModel $model
     * @param  mixed $data
     * @return TemplatePerguntaModel
     */
    public function update(TemplatePerguntaModel $model, array $data): TemplatePerguntaModel
    {
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Remove uma pergunta (caderno de campo)
     *
     * @param  mixed $model
     * @return bool
     */
    public function delete(TemplatePerguntaModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
