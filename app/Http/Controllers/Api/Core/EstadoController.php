<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\CidadeModel;
use DB;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    /**
     * Retorna a lista de cidades de acordo com o "id" do estado
     *
     * @param  Request $request
     * @return JSON
     */
    public function cidades(Request $request)
    {
        $data = $request->only(
            'id'
        );

        return response()->json([
            'cidades' => \App\Models\Core\CidadeModel::where('estado_id', $data['id'])->orderBy('nome', 'ASC')->get(['id', 'nome', 'estado_id'])
        ]);
    }

    /**
     * Retorna uma lista de cidades de acordo com "termo" passado
     *
     * @param  Request $request
     * @return void
     */
    public function cidadesBusca(Request $request)
    {
        $termo = $request->only('termo');
        $termo = $termo['termo'];

        $cidades = CidadeModel::join('estados', 'estados.id', 'cidades.estado_id')
            ->select('cidades.id', DB::raw('CONCAT(cidades.nome," - ", estados.uf) as nome_composto'), 'estado_id')
            ->where('cidades.nome', 'like', "%$termo%")->orderBy('cidades.nome', 'ASC')->get(['id', 'nome_composto', 'estado_id']);

        return response()->json([
            'cidades' => $cidades
        ]);
    }
}
