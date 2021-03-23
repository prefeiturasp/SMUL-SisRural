<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Core\TermosDeUsoModel;
use App\Repositories\Backend\Auth\UserRepository;
use Illuminate\Http\Request;

/**
 * Class HomeController.
 */
class HomeController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (auth()->check()) {
            return view('frontend.index');
        } else {
            return view('frontend.auth.login');
        }
    }

    /**
     * Página dos termos de uso
     */
    public function termosUso($acceptTerms = null)
    {
        $terms = TermosDeUsoModel::first();

        return view('frontend.termos-uso.index', compact('acceptTerms', 'terms'));
    }

    /**
     * Aceita os termos do usuário
     *
     * Só acessa essa rota se esta "autenticado" (middleware: auth)
     */
    public function storeTermosUso(Request $request, UserRepository $userRepository)
    {
        if (!$request->has('fl_accept_terms')) {
            return redirect(route('frontend.termos-de-uso', 1))->withFlashDanger("Você precisa aceitar os termos de uso para prosseguir.");
        }

        $user = $request->user();
        $userRepository->acceptTerms($user);

        return redirect(route(home_route()));
    }
}
