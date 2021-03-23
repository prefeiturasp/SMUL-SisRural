<?php

namespace App\Http\Controllers\Backend\Traits\Indicadores;

use App\Enums\CadernoStatusEnum;
use App\Helpers\General\AppHelper;
use Illuminate\Http\Request;

trait Chart_5_5_Caderno_Tecnico
{
    function getChart_5_5_Caderno_Tecnico(Request $request)
    {
        $query = $this->getQueryChart_5_5_Caderno_Tecnico($request);

        $data = $query->select('U.first_name', 'U.last_name', \DB::raw('count(cadernos.id) as count'))
            ->groupBy('U.first_name', 'U.last_name')
            ->get();

        return $data->map(function($v) {
            return [
                'nome' => $v['first_name'].' '.$v['last_name'],
                'count' => $v['count']
            ];
        })->toArray();
    }

    function getQueryChart_5_5_Caderno_Tecnico(Request $request)
    {
        $requestData = $this->service->getFilterData($request);

        //Mais próxima do resultado da 5_1, que usa somente cadernos finalizados
        //O chart 1_13b utiliza apenas o finish_user_id, mantido a mesma lógica
        $query = $this->chartService->getCadernos($requestData)
            ->where('cadernos.status', CadernoStatusEnum::Finalizado)
            ->join('users as U', 'U.id', '=', 'cadernos.finish_user_id')
            ->whereBetween('cadernos.updated_at', AppHelper::dateBetween($requestData['dt_ini'], $requestData['dt_end']));

        return $query;
    }
}
