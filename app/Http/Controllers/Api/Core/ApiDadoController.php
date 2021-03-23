<?php

namespace App\Http\Controllers\Api\Core;

use App\Enums\ProdutorUnidadeProdutivaStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiDadoController extends Controller
{
    /**
     * Retorna as unidades produtivas com base na especificação
     *
     * @param  mixed $request
     * @return JSON
     */
    public function unidadesProdutivas(Request $request)
    {
        $limit = 200;
        if (app()->environment() == "local") {
            $limit = 2;
        }

        /**
         * with() com os parametros que serão consimidos para otimizar a consulta. Dobra (2x) velocidade da consulta na api.
         * Ao adicionar novos campos com relacionamento, adicionar no "with"
         */
        $unidadesProdutivas = $request->user()->unidadesProdutivas()
            ->with('cidade:cidades.id,cidades.nome', 'estado:estados.id,estados.nome,estados.uf', 'canaisComercializacao:canal_comercializacoes.id', 'caracterizacoes:unidade_produtiva_caracterizacoes.id,unidade_produtiva_caracterizacoes.unidade_produtiva_id,solo_categoria_id,unidade_produtiva_caracterizacoes.area')
            ->with('produtoresWithoutGlobalScopes:produtores.id,produtores.uid,produtores.nome,cpf,email,telefone_1,telefone_2,fl_agricultor_familiar,fl_agricultor_familiar_dap,genero_id,data_nascimento,fl_internet,fl_tipo_parceria,tipo_parcerias_obs')
            ->where('status', ProdutorUnidadeProdutivaStatusEnum::Ativo)
            ->whereHas('produtoresWithoutGlobalScopes', function ($q) {
                $q->where("status", ProdutorUnidadeProdutivaStatusEnum::Ativo);
            })->simplePaginate($limit);

        $data = collect();

        foreach ($unidadesProdutivas->getCollection() as $unidadeProdutiva) {
            foreach ($unidadeProdutiva->produtoresWithoutGlobalScopes as $produtor) {
                $v = array();
                $v['unidade_produtiva_id'] = $unidadeProdutiva->id;
                $v['unidade_produtiva_uid'] = $unidadeProdutiva->uid;
                $v['produtor_id'] = $produtor->id;
                $v['produtor_uid'] = $produtor->uid;
                $v['produtor_nome'] = $produtor->nome;
                $v['produtor_cpf'] = $produtor->cpf;
                $v['unidade_produtiva_nome'] = $unidadeProdutiva->nome;
                $v['produtor_email'] = $produtor->email;
                $v['produtor_telefone_1'] = $this->normalizeTelefone($produtor->telefone_1);
                $v['produtor_telefone_2'] = $this->normalizeTelefone($produtor->telefone_2);
                $v['unidade_produtiva_endereco'] = $unidadeProdutiva->endereco;
                $v['unidade_produtiva_bairro'] = $unidadeProdutiva->bairro;
                $v['unidade_produtiva_cidade'] = $unidadeProdutiva->cidade->nome;
                $v['unidade_produtiva_estado'] = $unidadeProdutiva->estado->uf;
                $v['unidade_produtiva_cep'] = $this->normalizeCep($unidadeProdutiva->cep);
                $v['unidade_produtiva_producao'] = $this->getUnidadeProdutivaProducao($unidadeProdutiva);
                $v['unidade_produtiva_onde_comprar'] = $this->getUnidadeProdutivaOndeComprar($unidadeProdutiva);
                $v['unidade_produtiva_certificacao'] = $this->getUnidadeProdutivaCertificacoes($unidadeProdutiva);
                $v['unidade_produtiva_area_cultivada'] = $this->getUnidadeProdutivaAreaCultivada($unidadeProdutiva);
                $v['unidade_produtiva_area_total'] = $unidadeProdutiva->area_total_solo * 1;
                $v['produtor_agricultura_familiar'] = $produtor->fl_agricultor_familiar;
                $v['produtor_associacao'] = $this->getProdutorAssociacao($produtor);
                $v['produtor_dap'] = @$produtor->fl_agricultor_familiar_dap;
                $v['produtor_data_nascimento'] = $this->getProdutorDataNascimento($produtor);
                $v['produtor_genero'] = $produtor->genero_id;
                $v['produtor_acessa_internet'] = $produtor->fl_internet;
                $v['unidade_produtiva_lat'] = $unidadeProdutiva->lat;
                $v['unidade_produtiva_lng'] = $unidadeProdutiva->lng;

                $data->push($v);
            }
        }

        $unidadesProdutivas->setCollection($data);
        return response()->json($unidadesProdutivas);
    }

    /**
     * Normaliza o telefone p/ o formato XX XXXXXXXXX
     */
    private function normalizeTelefone($telefone)
    {
        if (!$telefone) {
            return null;
        }

        $normalize = str_replace(" ", "", $telefone);
        return substr($normalize, 0, 2) . " " . substr($normalize, 2, strlen($normalize));
    }

    /**
     * Normaliza o CEP, retornando vazio caso tenha sido cadastrado "00000-000"
     */
    private function normalizeCep($cep)
    {
        if (!$cep || $cep == "00000-000") {
            return null;
        }

        return $cep;
    }

    /**
     * Retorno das produções (uso do solo)
     */
    private function getUnidadeProdutivaProducao($unidadeProdutiva)
    {
        return join(",", @$unidadeProdutiva->caracterizacoes->pluck('solo_categoria_id')->unique()->toArray());
    }

    /**
     * Retorno dos canais de comercialização.
     *
     * Caso não tenha, deve retornar 0.
     */
    private function getUnidadeProdutivaOndeComprar($unidadeProdutiva)
    {
        $comercializacao = join(",", @$unidadeProdutiva->canaisComercializacao->pluck('id')->toArray());

        if (!$comercializacao) {
            return 0;
        }

        return $comercializacao;
    }

    /**
     * Retorno das certificações, caso não tenha, retornará ""
     */
    private function getUnidadeProdutivaCertificacoes($unidadeProdutiva)
    {
        return join(",", @$unidadeProdutiva->certificacoes->pluck('id')->toArray());
    }

    /**
     * Retorno do total de area cultivada, ignorando Pastagens, Pousio, Vegetação nativa (solo_categoria_id = 10,11,12)
     */
    private function getUnidadeProdutivaAreaCultivada($unidadeProdutiva)
    {
        $total = 0;
        foreach ($unidadeProdutiva->caracterizacoes as $v) {
            //Ignora Pastagem, Pousio, Vegetação Nativa
            if (in_array($v->solo_categoria_id, [10, 11, 12])) {
                continue;
            }

            $total += $v->area * 1;
        }

        $total = round($total * 100000) / 100000;

        return $total;
    }

    /**
     * Retorna as associações do produtor
     *
     * Caso não tenha parceria e nenhuma observação, deve retornar "Não"
     */
    private function getProdutorAssociacao($produtor)
    {
        if (!$produtor->fl_tipo_parceria || !$produtor->tipo_parcerias_obs) {
            return 'Não';
        } else {
            return $produtor->tipo_parcerias_obs;
        }
    }

    /**
     * Normaliza a data de nascimento p/ o formato DD/MM/YYYY
     */
    private function getProdutorDataNascimento($produtor)
    {
        if (!$produtor->data_nascimento) {
            return null;
        }

        return \Carbon\Carbon::parse($produtor->data_nascimento)->format('d/m/Y');
    }
}
