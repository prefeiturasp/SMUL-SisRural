<?php

namespace App\Http\Controllers\Api\Core\UnidadeProdutiva;

use App\Http\Controllers\Controller;
use App\Models\Core\ProdutorModel;
use App\Models\Core\RegiaoModel;
use App\Models\Core\UnidadeProdutivaModel;
use Illuminate\Http\Request;

class UnidadeProdutivaController extends Controller
{
    /**
     * Retorna as categorias e espécies de acordo com o "id" da categoria passada
     *
     * É utilizado na UnidadeProdutivaController / Caracterizacao (unidade_produtiva/caracterizacao/create_update)
     *
     * @param  mixed $request
     * @return JSON
     */
    public function soloCategorias(Request $request)
    {
        $data = $request->only(
            'id'
        );

        return response()->json([
            'solo_categoria' => \App\Models\Core\SoloCategoriaModel::find($data['id']),
        ]);
    }

    /**
     * Retorna uma lista de produtores (nome)
     *
     * @param  Request $request
     * @return void
     */
    public function produtores(Request $request)
    {
        $termo = $request->only('termo');
        $termo = $termo['termo'];

        $data = ProdutorModel::where('nome', 'like', "%$termo%")->orderBy('nome', 'ASC');

        return response()->json([
            'data' => $data->get(['id', 'nome'])
        ]);
    }

    /**
     * Retorna uma lista de unidade produtivas (nome)
     *
     * @param  Request $request
     * @return void
     */
    public function unidadesProdutivas(Request $request)
    {
        $termo = $request->only('termo');
        $termo = $termo['termo'];

        $data = UnidadeProdutivaModel::where('nome', 'like', "%$termo%")
            ->orWhere('socios', 'like', "%$termo%")
            ->orderBy('nome', 'ASC');

        return response()->json([
            'data' => $data->get(['id', 'nome'])
        ]);
    }

    /**
     * Retorna uma lista de regiões
     *
     * @param  Request $request
     * @return void
     */
    public function regioes(Request $request)
    {
        $regioes = RegiaoModel::get(['id', 'nome', 'poligono']);

        $data = [];
        foreach ($regioes as $k => $v) {
            $data[] = ['id' => $v->id, 'nome' => $v->nome, 'poligono' => $v->poligono->toWKT()];
        }

        return response()->json([
            'regioes' => $data
        ]);
    }
}
