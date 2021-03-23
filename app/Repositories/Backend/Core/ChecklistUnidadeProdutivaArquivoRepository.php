<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Helpers\General\GeoHelper;
use App\Models\Core\ChecklistUnidadeProdutivaArquivoModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class ChecklistUnidadeProdutivaArquivoRepository extends BaseRepository
{
    public function __construct(ChecklistUnidadeProdutivaArquivoModel $model)
    {
        $this->model = $model;
    }

    /**
     * Upload de um arquivo no formulário aplicado
     *
     * @param  mixed $file
     * @param  ChecklistUnidadeProdutivaArquivoModel $model
     * @return ChecklistUnidadeProdutivaArquivoModel
     */
    public function upload($file, ChecklistUnidadeProdutivaArquivoModel $model): ChecklistUnidadeProdutivaArquivoModel
    {
        if ($file->isValid()) {
            $geo = GeoHelper::extractLatLngFile($file->getPathName());

            $tipo = 'arquivo';
            if (strpos(@$file->getMimeType(), 'image') !== FALSE) {
                $tipo = 'imagem';
            }

            $path = 'checklist_unidade_produtiva_arquivos/' . $model->id . '.' . $file->getClientOriginalExtension();
            \Storage::put($path, \fopen($file->getRealPath(), 'r+'));

            $model->update(['nome' => $file->getClientOriginalName(), 'arquivo' => $path, 'tipo' => $tipo, 'lat' => $geo['lat'], 'lng' => $geo['lng']]);
        }

        return $model;
    }

    /**
     * Cria o arquivo
     *
     * @param  mixed $data
     * @return ChecklistUnidadeProdutivaArquivoModel
     */
    public function create(array $data): ChecklistUnidadeProdutivaArquivoModel
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
     * Atualiza o arquivo
     *
     * @param  ChecklistUnidadeProdutivaArquivoModel $model
     * @param  mixed $data
     * @return ChecklistUnidadeProdutivaArquivoModel
     */
    public function update(ChecklistUnidadeProdutivaArquivoModel $model, array $data): ChecklistUnidadeProdutivaArquivoModel
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
     * Remove o arquivo
     *
     * @param  ChecklistUnidadeProdutivaArquivoModel $model
     * @return bool
     */
    public function delete(ChecklistUnidadeProdutivaArquivoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
