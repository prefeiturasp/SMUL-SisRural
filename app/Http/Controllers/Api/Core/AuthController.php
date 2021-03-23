<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Auth\ForgotPasswordController;
use App\Models\Auth\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Login, retorna um JWT TOKEN
     *
     * Ver dados do token no arquivo "CustomAccessToken.php"
     *
     * @param  [string] cpf
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'document' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['document', 'password', 'id']);
        $credentials['document'] = preg_replace('/[^0-9]/', '', $credentials['document']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Usuário ou senha incorretos'
            ], 401);
        }

        $user = $request->user();

        if (!$user->isConfirmed()) {
            return response()->json([
                'message' => __('exceptions.frontend.auth.confirmation.pending')
            ], 401);
        }

        if (!$user->isActive()) {
            return response()->json([
                'message' => __('exceptions.frontend.auth.deactivated')
            ], 401);
        }

        if (!$user->isUnidOperacional() && !$user->isTecnico()) {
            return response()->json([
                'message' => 'Apenas Unidades Operacionais/Técnicos podem utilizar o aplicativo.'
            ], 401);
        }

        if ($user->can('report restricted')) {
            return response()->json([
                'message' => 'Apenas Unidades Operacionais/Técnicos habilitados podem utilizar o aplicativo.'
            ], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);

        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /**
     * Logout
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Dados do usuário
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Esqueci minha senha
     *
     * @return [string] message
     */
    public function forgot(Request $request)
    {
        $request->validate(['document' => 'required']);

        $forgotController = new ForgotPasswordController();

        $response = $forgotController->broker()->sendResetLink(preg_replace('/[^0-9]/', '', $request->only('document')));

        if ($response !== Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Não foi possível encontrar o CPF solicitado.'
            ], 401);
        }

        return response()->json([
            'message' => 'Foi enviado um email p/ recuperação da senha.'
        ]);
    }

    /**
     * Retorna as credenciais válidas para o CPF informado
     *
     * @return [json] user object
     */
    public function documentRoles(Request $request)
    {
        $document = preg_replace('/[^0-9]/', '', @$request->only('document'));

        $user = User::where("document", $document)->get();

        $return = [];
        if (count($user) > 0) {
            $return = $user->first()->allRoles();
        }

        return response()->json(['roles' => $return]);
    }
}
