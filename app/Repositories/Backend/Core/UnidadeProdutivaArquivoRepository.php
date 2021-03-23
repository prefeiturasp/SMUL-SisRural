<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Helpers\General\GeoHelper;
use App\Models\Core\UnidadeProdutivaArquivoModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class UnidadeProdutivaArquivoRepository extends BaseRepository
{
    public function __construct(UnidadeProdutivaArquivoModel $model)
    {
        $this->model = $model;
    }

    /**
     * Upload do arquivo, super parecido com o CadernoArquivoRepository
     *
     * @param  mixed $file
     * @param  mixed $UnidadeProdutivaArquivoModel
     * @return void
     */
    public function upload($file, UnidadeProdutivaArquivoModel $model)
    {
        if ($file->isValid()) {
            $geo = GeoHelper::extractLatLngFile($file->getPathName());

            $tipo = 'arquivo';
            if (strpos(@$file->getMimeType(), 'image') !== FALSE) {
                $tipo = 'imagem';
            }

            $path = 'unidade_produtiva_arquivos/' . $model->id . '.' . $file->getClientOriginalExtension();
            // \Storage::put($path, file_get_contents($file->getRealPath()));
            \Storage::put($path, \fopen($file->getRealPath(), 'r+'));

            // $path = $file->storeAs('unidade_produtiva_arquivos', $model->id . '.' . $file->getClientOriginalExtension());

            $model->update(['nome' => $file->getClientOriginalName(), 'arquivo' => $path, 'tipo' => $tipo, 'lat' => $geo['lat'], 'lng' => $geo['lng']]);
        }
    }

    /**
     * Cria/upload de um arquivo (Unidade Produtiva)
     *
     * @param  mixed $data
     * @return UnidadeProdutivaArquivoModel
     */
    public function create(array $data): UnidadeProdutivaArquivoModel
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
     * Atualiza um arquivo (Unidade Produtiva)
     *
     * @param  UnidadeProdutivaArquivoModel $model
     * @param  mixed $data
     * @return UnidadeProdutivaArquivoModel
     */
    public function update(UnidadeProdutivaArquivoModel $model, array $data): UnidadeProdutivaArquivoModel
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
     * Remove um arquivo (Unidade Produtiva)
     *
     * @param  UnidadeProdutivaArquivoModel $model
     * @return bool
     */
    public function delete(UnidadeProdutivaArquivoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
