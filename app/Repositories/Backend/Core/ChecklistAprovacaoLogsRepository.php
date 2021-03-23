<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\ChecklistAprovacaoLogsModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class ChecklistAprovacaoLogsRepository extends BaseRepository
{
    public function __construct(ChecklistAprovacaoLogsModel $model)
    {
        $this->model = $model;
    }

    /**
     * Cria um log no momento da análise de um formulário aplicado
     *
     * @param  mixed $data
     * @return ChecklistAprovacaoLogsModel
     */
    public function create(array $data): ChecklistAprovacaoLogsModel
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
     * Atualiza o log
     *
     * @param  ChecklistAprovacaoLogsModel $model
     * @param  mixed $data
     * @return ChecklistAprovacaoLogsModel
     *
     * @deprecated A principio esse método nunca é utilizado, porque depois que um log é gerado, não pode ser atualizado
     */
    public function update(ChecklistAprovacaoLogsModel $model, array $data): ChecklistAprovacaoLogsModel
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
     * Atualiza o log
     *
     * @param  ChecklistAprovacaoLogsModel $model
     * @param  mixed $data
     * @return ChecklistAprovacaoLogsModel
     *
     * @deprecated A principio esse método nunca é utilizado, porque depois que um log é gerado, não pode ser removido
     */
    public function delete(ChecklistAprovacaoLogsModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
