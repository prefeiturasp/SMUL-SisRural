<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Events\Backend\Auth\User\UserDeleted;
use App\Events\Frontend\Auth\UserLoggedIn;
use App\Helpers\General\AppHelper;
use App\Helpers\General\CacheHelper;
use App\Http\Controllers\Backend\Traits\UserRolesTrait;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Authenticate;
use App\Http\Requests\Backend\Auth\User\ManageUserRequest;
use App\Http\Requests\Backend\Auth\User\StoreUserRequest;
use App\Http\Requests\Backend\Auth\User\UpdateUserRequest;
use App\Models\Auth\Traits\Scope\UserPermissionScope;
use App\Models\Auth\User;
use App\Repositories\Backend\Auth\PermissionRepository;
use App\Repositories\Backend\Auth\RoleRepository;
use App\Repositories\Backend\Auth\UserRepository;
use App\Repositories\Backend\Core\DominioRepository;
use App\Repositories\Backend\Core\UnidadeOperacionalRepository;
use DataTables;

/**
 * Class UserController.
 */
class UserController extends Controller
{
    use UserRolesTrait;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param ManageUserRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ManageUserRequest $request)
    {
        return view('backend.auth.user.index')->withUsers($this->userRepository->getActivePaginated(25, 'id', 'asc'));
    }

    public function datatable()
    {
        //Tratamento especifico para Técnicos verem os suas Unidades Operacionais ... Não foi adicionado no permissionScope p/ não influenciar o resto do siste
        if (auth()->user()->isTecnico()) {
            $listUsers = User::withoutGlobalScope(UserPermissionScope::class)->unidadesOperacionaisComTecnicos()->query();
        } else {
            $listUsers = User::query();
        }

        $listUsers->with(['dominiosNS', 'unidadesOperacionaisNS']);

        //Se der estouro de memória, voltar para o User:query(), descomentar os filtros e setar "orderable":false no index.blade.php do User
        return DataTables::collection($listUsers->get())
            ->editColumn('first_name', function ($row) {
                return $row->full_name;
            })->addColumn('roles_label', function ($row) {
                return $row->roles_label;
            })->editColumn('document', function ($row) {
                return AppHelper::formatCpfCnpj($row->document);
            })->addColumn('dominio', function ($row) {
                return @CacheHelper::singleDominioRow($row)->nome; // @$row->singleDominio()->nome;

                if ($row->dominios->count() > 0) {
                    return join(", ", $row->dominios->pluck('nome')->toArray());
                } else {
                    return  join(", ", $row->unidadesOperacionais()->with('dominio')->get()->pluck('dominio.nome')->toArray());
                }
            })->addColumn('unidades_operacionais', function ($row) {
                return @join(", ", @$row->unidadesOperacionaisNS->pluck('nome')->toArray());
            })->addColumn('active', function ($row) {
                return boolean_sim_nao($row->active);
            })
            ->addColumn('actions', function ($row) {
                return view('backend.auth.user.includes.actions', ['user' => $row]);
            })
            ->make(true);
    }



    /**
     * @param ManageUserRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexAll(ManageUserRequest $request)
    {
        return view('backend.auth.user.all')->withUsers($this->userRepository->getActivePaginated(25, 'id', 'asc'));
    }

    public function datatableAll()
    {
        $listUsers = User::withoutGlobalScopes()->whereHas('roles', function ($q) {
            $q->where('name', '<>', config('access.users.admin_role'));
            $q->where('name', '<>', config('access.users.app_admin_role'));
        })->get();

        return DataTables::collection($listUsers)
            ->editColumn('first_name', function ($row) {
                return $row->full_name;
            })->addColumn('roles_label', function ($row) {
                return $row->roles_label;
            })->editColumn('document', function ($row) {
                return AppHelper::formatCpfCnpj($row->document);
            })->addColumn('dominio', function ($row) {
                return @$row->singleDominio()->nome;
            })->addColumn('unidades_operacionais', function ($row) {
                return @join(", ", @$row->unidadesOperacionaisNS->pluck('nome')->toArray());
            })->addColumn('active', function ($row) {
                return boolean_sim_nao($row->active);
            })->addColumn('actions', function ($row) {
                return view('backend.auth.user.includes.actions_index_all', ['user' => $row]);
            })
            ->make(true);
    }


    /**
     * @param ManageUserRequest $request
     * @param RoleRepository $roleRepository
     * @param PermissionRepository $permissionRepository
     * @param DominioRepository $dominioRepository
     * @param UnidadeOperacionalRepository $unidadeOperacionalRepository
     *
     * @return mixed
     */
    public function create(ManageUserRequest $request, RoleRepository $roleRepository, DominioRepository $dominioRepository, UnidadeOperacionalRepository $unidadeOperacionalRepository, PermissionRepository $permissionRepository)
    {
        return view('backend.auth.user.create')
            ->withRoles($roleRepository->with('permissions')->get(['id', 'name']))
            ->withDominios($dominioRepository->get()->pluck('nome', 'id'))
            ->withUnidadeOperacionais($unidadeOperacionalRepository->get()->pluck('nome', 'id'))
            ->withPermissions($permissionRepository->where('fl_domain_user', 1)->get(['id', 'name']));
    }

    /**
     * @param StoreUserRequest $request
     *
     * @return mixed
     * @throws \Throwable
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userRepository->create($request->only(
            'first_name',
            'last_name',
            'email',
            'password',
            'document',
            'phone',
            'address',
            'work',
            'active',
            'confirmed',
            'confirmation_email',
            'roles',
            'permissions'
        ));

        $roles = $request->only('roles');

        if ($roles['roles'][0] == config('access.users.domain_role')) {
            $this->userRepository->updateDominios($user, $request->get('dominios'));
        }

        if (
            $roles['roles'][0] == config('access.users.operational_unit_role') ||
            $roles['roles'][0] == config('access.users.technician_role')
        ) {
            $this->userRepository->updateUnidadesOperacionais($user, $request->get('unidades_operacionais'));
        }

        return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('alerts.backend.users.created'));
    }

    /**
     * @param ManageUserRequest $request
     * @param User $user
     *
     * @return mixed
     */
    public function show(ManageUserRequest $request, User $user)
    {
        return view('backend.auth.user.show')
            ->withUser($user);
    }

    /**
     * @param ManageUserRequest $request
     * @param RoleRepository $roleRepository
     * @param PermissionRepository $permissionRepository
     * @param User $user
     *
     * @return mixed
     */
    public function edit(ManageUserRequest $request, User $user, PermissionRepository $permissionRepository)
    {
        $rolesId = 'iframeRoles';
        $rolesSrc = route('admin.auth.user.roles.index', compact('user'));

        return view('backend.auth.user.edit', compact('rolesId', 'rolesSrc'))
            ->withUser($user)
            ->withPermissions($permissionRepository->where('fl_domain_user', 1)->get(['id', 'name']))
            ->withUserPermissions($user->permissions->pluck('name')->all());
    }

    /**
     * @param UpdateUserRequest $request
     * @param User $user
     *
     * @return mixed
     * @throws \Throwable
     * @throws \App\Exceptions\GeneralException
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userRepository->update($user, $request->only(
            'first_name',
            'last_name',
            'email',
            'document',
            'phone',
            'address',
            'work',
            'permissions'
        ));

        return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('alerts.backend.users.updated'));
    }

    /**
     * @param ManageUserRequest $request
     * @param User $user
     *
     * @return mixed
     * @throws \Exception
     */
    public function destroy(ManageUserRequest $request, User $user)
    {
        $this->userRepository->deleteById($user->id);

        event(new UserDeleted($user));

        return redirect()->route('admin.auth.user.deleted')->withFlashSuccess(__('alerts.backend.users.deleted'));
    }

    public function messages()
    {
        return [
            'unique' => 'O CPF já encontra-se utilizado.',
        ];
    }


    /**
     * Altera a "role", loga com o usuário correspondente (Se tiver permissão)
     */
    public function changeRole($userId)
    {
        $userLogged = auth()->user();
        $userGo = User::withoutGlobalScopes()->where('id', $userId)->get()->first();

        if ($userLogged->document == $userGo->document && $userLogged->email == $userGo->email) {
            if ($userGo->isConfirmed() && $userGo->isActive()) {
                auth()->login($userGo, true);

                event(new UserLoggedIn($userGo));

                return redirect()->route('admin.dashboard')->withFlashSuccess('Papel do usuário alterado com sucesso.');
            }
        }

        return redirect()->route('admin.dashboard')->withFlashDanger('Não foi possível alterar o papel do seu usuário.');
    }
}
