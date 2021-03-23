<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\UnidadeProdutivaRespostaArquivoModel;
use App\Models\Core\UnidadeProdutivaRespostaModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class UnidadeProdutivaRespostaArquivoRepository extends BaseRepository
{
    public function __construct(UnidadeProdutivaRespostaArquivoModel $model)
    {
        $this->model = $model;
    }

    /**
     * Upload de um arquivo na resposta da unidade produtiva (template do formulário aplicado possuí uma pergunta do tipo "anexo")
     *
     * @param  mixed $file
     * @param  UnidadeProdutivaRespostaArquivoModel $model
     * @return void
     */
    public function upload($file, UnidadeProdutivaRespostaArquivoModel $model)
    {
        if ($file->isValid()) {
            $path = 'unidade_produtiva_respostas/' . $model->id . '.' . $file->getClientOriginalExtension();
            // \Storage::put($path, file_get_contents($file->getRealPath()));
            \Storage::put($path, \fopen($file->getRealPath(), 'r+'));
            // $path = $file->storeAs('unidade_produtiva_arquivos', $model->id . '.' . $file->getClientOriginalExtension());

            $model->update(['arquivo' => $path]);

            $modelUnidadeProdutivaResposta = $model->unidadeProdutivaResposta;
            $modelUnidadeProdutivaResposta->resposta = $path;
            $modelUnidadeProdutivaResposta->save();
        }
    }

    /**
     * Criar uma resposta do tipo arquivo
     *
     * @param  mixed $data
     * @return UnidadeProdutivaRespostaArquivoModel
     */
    public function create(array $data): UnidadeProdutivaRespostaArquivoModel
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
     * Atualizar uma resposta do tipo arquivo
     *
     * @param  UnidadeProdutivaRespostaArquivoModel $model
     * @param  mixed $data
     * @return UnidadeProdutivaRespostaArquivoModel
     */
    public function update(UnidadeProdutivaRespostaArquivoModel $model, array $data): UnidadeProdutivaRespostaArquivoModel
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
     * Remover uma resposta do tipo arquivo
     *
     * @param  UnidadeProdutivaRespostaArquivoModel $model
     * @return bool
     */
    public function delete(UnidadeProdutivaRespostaArquivoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
