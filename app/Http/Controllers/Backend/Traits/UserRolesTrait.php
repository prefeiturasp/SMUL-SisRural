<?php

namespace App\Http\Controllers\Backend\Traits;

use App\Events\Backend\Auth\User\UserDeleted;
use App\Http\Controllers\Backend\Forms\UserRolesForm;
use App\Models\Auth\User;
use App\Repositories\Backend\Auth\RoleRepository;
use App\Repositories\Backend\Core\DominioRepository;
use App\Repositories\Backend\Core\UnidadeOperacionalRepository;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

trait UserRolesTrait
{
    use FormBuilderTrait;

    /**
     * Listagem de habilidades/papéis de um usuário
     *
     * @param  User $user
     * @param  Request $request
     * @return void
     */
    public function rolesIndex(User $user, Request $request)
    {
        $title = 'Habilidades';
        $urlAdd = route('admin.auth.user.roles.create', ["user" => $user]);
        $urlDatatable = route('admin.auth.user.roles.datatable', ["user" => $user]);

        return view('backend.auth.user.roles.index', compact('urlAdd', 'urlDatatable', 'title'));
    }

    /**
     * API Datatable "rolesIndex()"
     *
     * @param  User $user
     * @return void
     */
    public function rolesDatatable(User $user)
    {
        return DataTables::of($user->userRoles())
            ->addColumn('roles_label', function ($row) {
                return $row->roles_label;
            })->addColumn('dominio', function ($row) {
                return @$row->singleDominio()->nome;
            })->addColumn('unidades_operacionais', function ($row) {
                return @join(", ", @$row->unidadesOperacionaisNS->pluck('nome')->toArray());
            })->addColumn('active', function ($row) {
                return boolean_sim_nao($row->active);
            })
            ->addColumn('actions', function ($row) use ($user) {
                $params = ['user' => $user, 'userRole' => $row];

                $editUrl = route('admin.auth.user.roles.edit', $params);
                $deleteUrl = route('admin.auth.user.roles.destroy', $params);

                return view('backend.auth.user.roles.form-actions', ['editUrl' => $editUrl, 'deleteUrl' => $deleteUrl, 'row' => $row]);
            })
            ->make(true);
    }

    /**
     * Cadastro de habilidades (role)
     *
     * @param  mixeUserd $user
     * @param  FormBuilder $formBuilder
     * @param  RoleRepository $roleRepository
     * @param  DominioRepository $dominioRepository
     * @param  UnidadeOperacionalRepository $unidadeOperacionalRepository
     * @return void
     */
    public function rolesCreate(User $user, FormBuilder $formBuilder, RoleRepository $roleRepository, DominioRepository $dominioRepository, UnidadeOperacionalRepository $unidadeOperacionalRepository)
    {
        $form = $formBuilder->create(UserRolesForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.auth.user.roles.store', ['user' => $user]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => [
                'roles' =>  $roleRepository->get()->pluck('formatted_name', 'name')->toArray(),
                'dominios' => $dominioRepository->get()->pluck('nome', 'id')->toArray(),
                'unidades_operacionais' => $unidadeOperacionalRepository->get()->pluck('nome', 'id')->toArray()
            ]
        ]);

        $title = 'Adicionar Habilidade';

        $back = route('admin.auth.user.roles.index', compact('user'));

        return view('backend.auth.user.roles.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Cadastro de habilidades (role) - POST
     *
     * @param  mixed $request
     * @param  mixed $user
     * @return void
     */
    public function rolesStore(Request $request, User $user)
    {
        $form = $this->form(UserRolesForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $this->userRepository->createUserAndRole($user, $request->all());

        return redirect()->route('admin.auth.user.roles.index', compact('user'))->withFlashSuccess('Habilidade adicionada com sucesso!');
    }

    /**
     * Edição das roles de um usuário.
     *
     * Só permite adicionar/remover "Unidades Operacionais", os outros dados não são permitidos alterar.
     *
     * @param  User $user
     * @param  User $userRole
     * @param  FormBuilder $formBuilder
     * @param  RoleRepository $roleRepository
     * @param  DominioRepository $dominioRepository
     * @param  UnidadeOperacionalRepository $unidadeOperacionalRepository
     * @return void
     */
    public function rolesEdit(User $user, User $userRole, FormBuilder $formBuilder, RoleRepository $roleRepository, DominioRepository $dominioRepository, UnidadeOperacionalRepository $unidadeOperacionalRepository)
    {
        $form = $formBuilder->create(UserRolesForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.auth.user.roles.update', ['user' => $user, 'userRole' => $userRole]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => ['id' => $userRole->id, 'dominio' => @$userRole->singleDominio()->id, 'role' => @$userRole->roles->first()->name, 'unidades_operacionais' => @$userRole->unidadesOperacionais()->get()->pluck('id')->toArray()],
            'data' => [
                'roles' =>  $roleRepository->get()->pluck('formatted_name', 'name')->toArray(),
                'dominios' => $dominioRepository->get()->pluck('nome', 'id')->toArray(),
                'unidades_operacionais' => $unidadeOperacionalRepository->get()->pluck('nome', 'id')->toArray()
            ]
        ]);

        $title = 'Editar Habilidade';

        $back = route('admin.auth.user.roles.index', compact('user'));

        return view('backend.auth.user.roles.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Edição roles - POST
     *
     * @param  User $user
     * @param  User $userRole
     * @param  Request $request
     * @return void
     */
    public function rolesUpdate(User $user, User $userRole, Request $request)
    {
        $form = $this->form(UserRolesForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $userRole = $this->userRepository->updateUserAndRole($userRole, $request->all());

        return redirect()->route('admin.auth.user.roles.index', compact('user'))->withFlashSuccess('Habilidade atualizada com sucesso!');
    }

    /**
     * Remover uma role (na prática o usuário é "desativado")
     *
     * Desativa o usuário porque cada "role" esta ligada a um "único" usuário.
     *
     * Usuário pertence ao "Domínio ATER" e "Domínio PSA", significa que temos 2 usuários (clones)
     *
     * @param  User $user
     * @param  User $userRole
     * @return void
     */
    public function rolesDestroy(User $user, User $userRole)
    {
        $this->userRepository->deleteById($userRole->id);

        event(new UserDeleted($user));

        return redirect()->route('admin.auth.user.roles.index', compact('user'))->withFlashSuccess('Usuário removido com sucesso');
    }

    /**
     * Bloco de habilidade/role p/ listagem de usuários fora do Escopo do usuário
     *
     * @param  User $userAll
     * @param  FormBuilder $formBuilder
     * @param  RoleRepository $roleRepository
     * @param  DominioRepository $dominioRepository
     * @param  UnidadeOperacionalRepository $unidadeOperacionalRepository
     * @return void
     */
    public function rolesCreateUserAll(User $userAll, FormBuilder $formBuilder, RoleRepository $roleRepository, DominioRepository $dominioRepository, UnidadeOperacionalRepository $unidadeOperacionalRepository)
    {
        $form = $formBuilder->create(UserRolesForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.auth.user.roles.store.userAll', ['userAll' => $userAll]),
            'class' => 'needs-validation',
            'novalidate' => true,
            'data' => [
                'roles' =>  $roleRepository->get()->pluck('name', 'name')->toArray(),
                'dominios' => $dominioRepository->get()->pluck('nome', 'id')->toArray(),
                'unidades_operacionais' => $unidadeOperacionalRepository->get()->pluck('nome', 'id')->toArray()
            ]
        ]);

        $title = 'Adicionar Habilidade';

        $back = route('admin.auth.user.indexAll');

        return view('backend.auth.user.roles.create_update_user_all', compact('form', 'title', 'back'));
    }

    /**
     * Persiste uma "nova role" em um usuário (vai criar um novo usuário)
     *
     * Utilizado por "rolesCreateUserAll()"
     *
     * @param  Request $request
     * @param  User $userAll
     * @return mixed
     */
    public function rolesStoreUserAll(Request $request, User $userAll)
    {
        $form = $this->form(UserRolesForm::class);
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $this->userRepository->createUserAndRole($userAll, $request->all());

        return redirect()->route('admin.auth.user.indexAll')->withFlashSuccess('Habilidade adicionada com sucesso!');
    }
}
