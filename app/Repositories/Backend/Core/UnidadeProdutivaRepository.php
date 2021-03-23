<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Auth\Traits\Scope\UserPermissionScope;
use App\Models\Auth\User;
use App\Models\Core\CidadeModel;
use App\Models\Core\UnidadeProdutivaModel;
use App\Repositories\BaseRepository;
use App\Services\UnidadeProdutivaService;
use Auth;
use Illuminate\Support\Facades\DB;

class UnidadeProdutivaRepository extends BaseRepository
{
    public $offlineMethod = false; //Atributo utilizado pelo Sync Offline Mobile

    public function __construct(UnidadeProdutivaModel $model, UnidadeProdutivaService $service)
    {
        $this->model = $model;
        $this->service = $service;
    }

    /**
     * Extrai a lat/lng através da Cidade (cidade_id)
     *
     * @param  array $model
     * @return array
     */
    private function getLatLng($model)
    {
        $cidade = CidadeModel::where("id", $model['cidade_id'])->first();
        return ['lat' => $cidade['lat'], 'lng' => $cidade['lng']];
    }

    /**
     * Cria uma unidade produtiva
     *
     * - Ao criar é verificado se a lat/lng (cidade/estado) esta na abrangência do usuário (caso não esteja, não permite cadastrar)
     *
     * @param  mixed $data
     * @return UnidadeProdutivaModel
     */
    public function create(array $data): UnidadeProdutivaModel
    {
        return DB::transaction(function () use ($data) {
            if (!$data['lat'] || !$data['lng']) {
                $latLng = $this->getLatLng($data);
                $data['lat'] = $latLng['lat'];
                $data['lng'] = $latLng['lng'];
            }

            if (!is_numeric($data['lat']) || !is_numeric($data['lng'])) {
                throw new GeneralException('Formato da Latitude/Longitude inválida. Esperado: XX.XXXXX');
            }

            //Verifica se a lat/lng esta contida na abrangência permitida para o usuário visualizar
            $fl_consulta_abrangencia = $this->service->consultaAbrangencia($data['lat'], $data['lng']);

            //Não faz o tratamento no mobile, porque ele pode manipular o registro depois de salvar. Dispara o erro apenas no CMS
            if (!$this->offlineMethod && !$fl_consulta_abrangencia && !\Auth::user()->isAdmin()) {
                throw new GeneralException('Unidade produtiva fora da área de abrangência permitida');
            }

            //Se é mobile, se esta fora da área de abrangencia, flagueia o registro
            if ($this->offlineMethod && !$fl_consulta_abrangencia) {
                $data['fl_fora_da_abrangencia_app'] = 1;

                $user = (session('auth_user_id')) ? User::withoutGlobalScope(UserPermissionScope::class)->findOrFail(session('auth_user_id')) : Auth::user();
                $data['owner_id'] = $user->id;
            }

            $model = $this->model::create($data);

            if ($model) {
                //Vincula Produtor a Unidade Produtiva
                if (@$data['produtor_id'] && @$data['tipo_posse_id']) {
                    $model->produtoresWithTrashed()->syncWithoutDetaching([$data['produtor_id'] => ['tipo_posse_id' => $data['tipo_posse_id']]]);
                }

                //Atualiza as abrangencias (unidades operacionais vs unidade produtiva)
                $this->service->syncAbrangencias($model);

                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualização de uma unidade produtiva
     *
     * @param  UnidadeProdutivaModel $model
     * @param  mixed $data
     * @return UnidadeProdutivaModel
     */
    public function update(UnidadeProdutivaModel $model, array $data): UnidadeProdutivaModel
    {
        return DB::transaction(function () use ($model, $data) {
            if (!$data['lat'] || !$data['lng']) {
                $latLng = $this->getLatLng($data);
                $data['lat'] = $latLng['lat'];
                $data['lng'] = $latLng['lng'];
            }

            //Só faz a verificação se a LAT/LNG altera ... tratamento foi adicionado por causa que agora é possível adicionar Unidades Produtivas (Unidade Operacional) fora da abrangecia do usuário
            if ($model->lat != $data['lat'] || $model->lng != $data['lng']) {
                //Verifica se a lat/lng esta contida na abrangência permitida para o usuário visualizar
                $fl_consulta_abrangencia = $this->service->consultaAbrangencia($data['lat'], $data['lng']);

                //Não faz o tratamento no mobile, porque ele pode manipular o registro depois de salvar. Dispara o erro apenas no CMS
                if (!$this->offlineMethod && !$fl_consulta_abrangencia && !\Auth::user()->isAdmin()) {
                    throw new GeneralException('Unidade produtiva fora da área de abrangência permitida');
                }

                //Se é mobile, se esta fora da área de abrangencia, flagueia o registro
                if ($this->offlineMethod && !$fl_consulta_abrangencia) {
                    $data['fl_fora_da_abrangencia_app'] = 1;
                    $user = (session('auth_user_id')) ? User::withoutGlobalScope(UserPermissionScope::class)->findOrFail(session('auth_user_id')) : Auth::user();
                    $data['owner_id'] = $user->id;
                }

                //Se esta dentro da área de abrangencia, força o "fl_fora_..." p/ false, independente do usuário, significa que já foi revisionado
                if ($fl_consulta_abrangencia || \Auth::user()->isAdmin()) {
                    $data['owner_id'] = null; //Não precisa mais do OwnerId porque o registro já foi vinculado ao domínio do usuário logado.
                    $data['fl_fora_da_abrangencia_app'] = null;
                }
            }

            $model->update($data);

            //Atualiza as abrangencias (unidades operacionais vs unidade produtiva)
            $this->service->syncAbrangencias($model);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Atualiza o croqui da unidade produtiva (é um campo fixo na unidade produtiva de upload de arquivo)
     *
     * @param  mixed $file
     * @param  UnidadeProdutivaModel $model
     * @return void
     */
    public function uploadCroqui($file, UnidadeProdutivaModel $model)
    {
        if ($file->isValid()) {
            $path = 'croqui_propriedade/' . $model->id . '.' . $file->getClientOriginalExtension();
            // \Storage::put($path, file_get_contents($file->getRealPath()));
            \Storage::put($path, \fopen($file->getRealPath(), 'r+'));

            // $path = $file->storeAs('croqui_propriedade', $model->id . '.' . $file->getClientOriginalExtension(), ['disk' => 'public']);

            $model->update(['croqui_propriedade' => $path]);
        }
    }

    /**
     * Remoção de uma unidade produtiva
     *
     * @param  UnidadeProdutivaModel $model
     * @return bool
     */
    public function delete(UnidadeProdutivaModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    /**
     * Vincula unidades operacionais a unidades produtivas
     *
     * @param  UnidadeProdutivaModel $model
     * @param  array $unidadesOperacionais
     * @return void
     */
    public function unidadesOperacionais(UnidadeProdutivaModel $model, array $unidadesOperacionais)
    {
        return DB::transaction(function () use ($model, $unidadesOperacionais) {
            $model->unidadesOperacionais()->sync($unidadesOperacionais);
            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
