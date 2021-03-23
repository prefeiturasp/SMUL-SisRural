<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\TemplateModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class TemplateCadernoRepository extends BaseRepository
{
    public function __construct(TemplateModel $model)
    {
        $this->model = $model;
    }

    /**
     * Criação de um template do caderno de campo
     *
     * Só permite um template de caderno por Domínio
     *
     * @param  mixed $data
     * @return TemplateModel
     */
    public function create(array $data): TemplateModel
    {

        return DB::transaction(function () use ($data) {
            //Caso já exista um template no caderno de campo, não permite criar outro.
            $existCaderno = $this->model->where('dominio_id', @$data['dominio_id'])->where('tipo', 'caderno')->first();
            if (@$existCaderno) {
                throw new GeneralException('Não foi possível adicionar um novo Caderno de Campo. O Domínio selecionado já possuí um Caderno de Campo.');
            }

            $model = $this->model::create([
                'nome' => $data['nome'],
                'dominio_id' => $data['dominio_id'],
                'tipo' => $data['tipo'],
            ]);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualização de um template do caderno de campo
     *
     * @param  TemplateModel $model
     * @param  mixed $data
     * @return TemplateModel
     */
    public function update(TemplateModel $model, array $data): TemplateModel
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
     * Remover um template
     *
     * @param  TemplateModel $model
     * @return bool
     *
     * @deprecated Essa função provavelmente não é utilizada
     */
    public function delete(TemplateModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
