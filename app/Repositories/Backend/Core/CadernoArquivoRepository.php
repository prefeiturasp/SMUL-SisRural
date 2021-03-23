<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Helpers\General\GeoHelper;
use App\Models\Core\CadernoArquivoModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class CadernoArquivoRepository extends BaseRepository
{
    public function __construct(CadernoArquivoModel $model)
    {
        $this->model = $model;
    }

    /**
     * Upload de um arquivo no caderno de campo
     *
     * É extraído a LAT/LNG caso o arquivo possua (metadata)
     *
     * O tipo do arquivo (imagem/arquivo) é utilizado no APP
     *
     * @param  mixed $file
     * @param  mixed $model
     * @return CadernoArquivoModel
     */
    public function upload($file, CadernoArquivoModel $model): CadernoArquivoModel
    {
        if ($file->isValid()) {
            $geo = GeoHelper::extractLatLngFile($file->getPathName());

            $tipo = 'arquivo';
            if (strpos(@$file->getMimeType(), 'image') !== FALSE) {
                $tipo = 'imagem';
            }


            $path = 'caderno_arquivos/' . $model->id . '.' . $file->getClientOriginalExtension();
            \Storage::put($path, \fopen($file->getRealPath(), 'r+'));

            $model->update(['nome' => $file->getClientOriginalName(), 'arquivo' => $path, 'tipo' => $tipo, 'lat' => $geo['lat'], 'lng' => $geo['lng']]);
        }

        return $model;
    }

    /**
     * Cria o arquivo
     *
     * @param  mixed $data
     * @return CadernoArquivoModel
     */
    public function create(array $data): CadernoArquivoModel
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
     * @param  mixed $model
     * @param  mixed $data
     * @return CadernoArquivoModel
     */
    public function update(CadernoArquivoModel $model, array $data): CadernoArquivoModel
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
     * @param  mixed $model
     * @return bool
     */
    public function delete(CadernoArquivoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
