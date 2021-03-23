<?php

namespace App\Http\Controllers\Api\Core\Traits;

use App\Models\Core\CidadeModel;
use App\Models\Core\EstadoModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class UserScope.
 */
trait OfflineRegioesTrait
{

    public function regioes(Request $request)
    {
        $data = [];

        $data['estados'] = EstadoModel::whereUpdatedAt($request->input('updated_at_estados'))->get(['id', 'nome', 'uf', 'created_at', 'updated_at'])->toArray();

        $data['cidades'] = CidadeModel::whereUpdatedAt($request->input('updated_at_cidades'))->get(['id', 'nome', 'estado_id', 'created_at', 'updated_at'])->toArray();

        return response()->json([
            'data' => $data,
            'date' => Carbon::now()->toIso8601String()
        ], 200, array(), JSON_PRETTY_PRINT);
    }
}
