<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Models\Core\CidadeModel;
use App\Models\Core\UnidadeProdutivaModel;
use App\Repositories\BaseRepository;
use App\Services\UnidadeProdutivaService;
use Illuminate\Support\Facades\DB;

class NovoProdutorUnidadeProdutivaRepository extends BaseRepository
{
    public function __construct(ProdutorRepository $produtorRepository, UnidadeProdutivaRepository $unidadeProdutivaRepository, UnidadeProdutivaService $service)
    {
        $this->produtorRepository = $produtorRepository;
        $this->unidadeProdutivaRepository = $unidadeProdutivaRepository;
        $this->service = $service;
    }

    /**
     * Retorna a latitude/longitude de acordo com a Cidade
     *
     * @param  array $model
     * @return void
     */
    private function getLatLng($data)
    {
        $cidade = CidadeModel::where("id", $data['cidade_id'])->first();
        return ['lat' => $cidade['lat'], 'lng' => $cidade['lng']];
    }

    /**
     * Cadastro rápido para Produtor/Unidade Produtiva
     *
     * - Produtor
     * - Unidade Produtiva (caso seja uma nova)
     * - Vincula a Unidade Produtiva com o Produtor
     *
     * @param  mixed $data
     * @return array
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $produtorData = array('nome' => $data['nome_produtor'], 'cpf' => $data['cpf'], 'telefone_1' => $data['telefone_1'], 'telefone_2' => $data['telefone_2']);

            $enderecoData = array('cep' => $data['cep'], 'bairro' => $data['bairro'], 'endereco' => $data['endereco'], 'cidade_id' => @$data['cidade_id'], 'estado_id' => $data['estado_id']);

            $produtor = $this->produtorRepository->create($produtorData);

            //Se já existe a Unidade Produtiva
            if (@$data['unidade_produtiva_id'] && @$data['fl_unidade_produtiva']) {
                $unidadeProdutiva = UnidadeProdutivaModel::where('id', $data['unidade_produtiva_id'])->first();
                $produtor->unidadesProdutivasWithTrashed()->syncWithoutDetaching([$unidadeProdutiva->id => ['tipo_posse_id' => $data['tipo_posse_id']]]);
            } else {
                if (!$data['lat'] || !$data['lng']) {
                    $latLng = $this->getLatLng($data);
                    $data['lat'] = $latLng['lat'];
                    $data['lng'] = $latLng['lng'];
                }

                if (!is_numeric($data['lat']) || !is_numeric($data['lng'])) {
                    throw new GeneralException('Formato da Latitude/Longitude inválida. Esperado: XX.XXXXX');
                }

                //Verifica a abrangência
                if (!$this->service->consultaAbrangencia($data['lat'], $data['lng']) && !\Auth::user()->isAdmin()) {
                    throw new GeneralException('Unidade produtiva fora da área de abrangência permitida');
                }

                $unidadeProdutivaData = array('produtor_id' => $produtor->id, 'tipo_posse_id' => $data['tipo_posse_id'], 'nome' => $data['nome_unidade_produtiva'], 'lat' => $data['lat'], 'lng' => $data['lng']);
                $unidadeProdutiva = $this->unidadeProdutivaRepository->create(array_merge($enderecoData, $unidadeProdutivaData));
            }

            //fixa o estado/cidade no produtor
            $produtor->estado_id = $unidadeProdutiva->estado_id;
            $produtor->cidade_id = $unidadeProdutiva->cidade_id;
            $produtor->save();

            return array('produtor' => $produtor, 'unidadeProdutiva' => $unidadeProdutiva);

            throw new GeneralException('Favor tentar novamente');
        });
    }
}
