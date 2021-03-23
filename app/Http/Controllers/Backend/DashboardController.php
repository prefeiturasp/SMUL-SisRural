<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Core\CadernoModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\PlanoAcaoModel;
use App\Models\Core\ProdutorModel;
use App\Models\Core\UnidadeProdutivaModel;

/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    /**
     * Retorno do dashboard principal do CMS
     *
     * @return void
     */
    public function index()
    {
        $totalCaderno = CadernoModel::count();
        $totalProdutor = ProdutorModel::count();
        $totalUnidProdutiva = UnidadeProdutivaModel::count();
        $totalFormulariosAplicados = ChecklistUnidadeProdutivaModel::count();

        $totalPlanoAcao = PlanoAcaoModel::individual()->count();
        $totalPlanoAcaoColetivo = PlanoAcaoModel::coletivo()->count();

        return view('backend.dashboard', compact('totalCaderno', 'totalProdutor', 'totalUnidProdutiva', 'totalFormulariosAplicados', 'totalPlanoAcao', 'totalPlanoAcaoColetivo'));
    }
}
