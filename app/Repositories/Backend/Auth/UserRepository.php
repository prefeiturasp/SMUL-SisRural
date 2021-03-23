<?php

namespace App\Repositories\Backend\Auth;

use App\Events\Backend\Auth\User\UserConfirmed;
use App\Events\Backend\Auth\User\UserCreated;
use App\Events\Backend\Auth\User\UserDeactivated;
use App\Events\Backend\Auth\User\UserPasswordChanged;
use App\Events\Backend\Auth\User\UserPermanentlyDeleted;
use App\Events\Backend\Auth\User\UserReactivated;
use App\Events\Backend\Auth\User\UserRestored;
use App\Events\Backend\Auth\User\UserUnconfirmed;
use App\Events\Backend\Auth\User\UserUpdated;
use App\Exceptions\GeneralException;
use App\Models\Auth\User;
use App\Models\Core\UnidadeOperacionalModel;
use App\Notifications\Backend\Auth\UserAccountActive;
use App\Notifications\Frontend\Auth\UserNeedsConfirmation;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getUnconfirmedCount(): int
    {
        return $this->model
            ->where('confirmed', false)
            ->count();
    }

    /**
     * @param int $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */
    public function getActivePaginated($paged = 25, $orderBy = 'created_at', $sort = 'desc'): LengthAwarePaginator
    {
        return $this->model
            ->with('roles', 'permissions', 'providers')
            ->active()
            ->orderBy($orderBy, $sort)
            ->paginate($paged);
    }

    /**
     * @param int $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return LengthAwarePaginator
     */
    public function getInactivePaginated($paged = 25, $orderBy = 'created_at', $sort = 'desc'): LengthAwarePaginator
    {
        return $this->model
            ->with('roles', 'permissions', 'providers')
            ->active(false)
            ->orderBy($orderBy, $sort)
            ->paginate($paged);
    }

    /**
     * @param int $paged
     * @param string $orderBy
     * @param string $sort
     *
     * @return LengthAwarePaginator
     */
    public function getDeletedPaginated($paged = 25, $orderBy = 'created_at', $sort = 'desc'): LengthAwarePaginator
    {
        return $this->model
            ->with('roles', 'permissions', 'providers')
            ->onlyTrashed()
            ->orderBy($orderBy, $sort)
            ->paginate($paged);
    }

    /**
     * Criação um usuário
     *
     * @param array $data
     *
     * @return User
     * @throws \Throwable
     * @throws \Exception
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = $this->model::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'document' => preg_replace('/[^0-9]/', '', $data['document']),
                'password' => $data['password'],
                'active' => isset($data['active']) && $data['active'] === '1',
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
                'confirmed' => true, //isset($data['confirmed']) && $data['confirmed'] === '1',
                'phone' => $data['phone'],
                'address' => $data['address'],
                'work' => $data['work'],
            ]);

            // See if adding any additional permissions
            if (!isset($data['permissions']) || !count($data['permissions'])) {
                $data['permissions'] = [];
            }

            if ($user) {
                // User must have at least one role
                if (!count($data['roles'])) {
                    throw new GeneralException(__('exceptions.backend.access.users.role_needed_create'));
                }

                // Add selected roles/permissions
                $user->syncRoles($data['roles']);

                if (auth()->user()->isDominio() || auth()->user()->isAdmin() || auth()->user()->isAdminLOP()) {
                    $user->syncPermissions($data['permissions']);
                }

                //Send confirmation email if requested and account approval is off
                if ($user->confirmed === false && isset($data['confirmation_email']) && !config('access.users.requires_approval')) {
                    $user->notify(new UserNeedsConfirmation($user->confirmation_code));
                }

                event(new UserCreated($user));

                return $user;
            }

            throw new GeneralException(__('exceptions.backend.access.users.create_error'));
        });
    }

    /**
     * Atualização de um usuário
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     * @throws \Exception
     * @throws \Throwable
     * @throws GeneralException
     */
    public function update(User $user, array $data): User
    {
        $this->checkUserByEmail($user, $data['email'], $data['document']);

        $this->checkUserByDocument($user, $data['document'], $data['email']);

        // See if adding any additional permissions
        if (!isset($data['permissions']) || !count($data['permissions'])) {
            $data['permissions'] = [];
        }

        return DB::transaction(function () use ($user, $data) {
            if (auth()->user()->isDominio() || auth()->user()->isAdmin() || auth()->user()->isAdminLOP()) {
                $user->syncPermissions($data['permissions']);
            }

            $customData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'document' => preg_replace('/[^0-9]/', '', $data['document']),
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'work' => $data['work'],
            ];

            //Técnico não pode alterar seu cpf
            if (auth()->user()->isTecnico()) {
                unset($customData['document']);
            }

            if ($user->update($customData)) {

                //Atualiza as "cópias"
                $users = User::withoutGlobalScopes()->where('document', $data['document'])->where('id', '!=', $user->id)->get();
                foreach ($users as $v) {
                    $v->update($customData);
                }

                event(new UserUpdated($user));

                return $user;
            }

            throw new GeneralException(__('exceptions.backend.access.users.update_error'));
        });
    }

    /**
     * Atualização da senha de um usuário
     *
     * @param User $user
     * @param      $input
     *
     * @return User
     * @throws GeneralException
     */
    public function updatePassword(User $user, $input): User
    {
        if ($user->update(['password' => $input['password']])) {

            //Atualiza as "cópias"
            $users = User::withoutGlobalScopes()->where('document', $user->document)->where('id', '!=', $user->id)->get();
            foreach ($users as $k => $v) {
                $v->password = $input['password'];
                $v->password_changed_at = now();
                $v->save();
            }

            event(new UserPasswordChanged($user));

            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.update_password_error'));
    }

    /**
     * Aceitar os termos de uso
     */
    public function acceptTerms(User $user): User
    {
        if ($user->update(['fl_accept_terms' => 1])) {

            //Atualiza as "cópias"
            $users = User::withoutGlobalScopes()->where('document', $user->document)->where('id', '!=', $user->id)->get();
            foreach ($users as $k => $v) {
                $v->fl_accept_terms = 1;
                $v->save();
            }

            return $user;
        }

        throw new GeneralException('Não foi possível aceitar os termos de uso, tente novamente mais tarde!');
    }

    /**
     * Ativar/desativar um usuário
     *
     * @param User $user
     * @param      $status
     *
     * @return User
     * @throws GeneralException
     */
    public function mark(User $user, $status): User
    {
        if ($status === 0 && auth()->id() === $user->id) {
            throw new GeneralException(__('exceptions.backend.access.users.cant_deactivate_self'));
        }

        $user->active = $status;

        switch ($status) {
            case 0:
                event(new UserDeactivated($user));
                break;
            case 1:
                event(new UserReactivated($user));
                break;
        }

        if ($user->save()) {
            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.mark_error'));
    }

    /**
     * Confirmar um usuário
     *
     * @param User $user
     *
     * @return User
     * @throws GeneralException
     *
     * @deprecated No projeto o usuário não precisa confirmar o email.
     */
    public function confirm(User $user): User
    {
        if ($user->confirmed) {
            throw new GeneralException(__('exceptions.backend.access.users.already_confirmed'));
        }

        $user->confirmed = true;
        $confirmed = $user->save();

        if ($confirmed) {
            event(new UserConfirmed($user));

            // Let user know their account was approved
            if (config('access.users.requires_approval')) {
                $user->notify(new UserAccountActive);
            }

            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.cant_confirm'));
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws GeneralException
     *
     * @deprecated No projeto o usuário não precisa confirmar o email.
     *
     */
    public function unconfirm(User $user): User
    {
        if (!$user->confirmed) {
            throw new GeneralException(__('exceptions.backend.access.users.not_confirmed'));
        }

        if ($user->id === 1) {
            // Cant un-confirm admin
            throw new GeneralException(__('exceptions.backend.access.users.cant_unconfirm_admin'));
        }

        if ($user->id === auth()->id()) {
            // Cant un-confirm self
            throw new GeneralException(__('exceptions.backend.access.users.cant_unconfirm_self'));
        }

        $user->confirmed = false;
        $unconfirmed = $user->save();

        if ($unconfirmed) {
            event(new UserUnconfirmed($user));

            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.cant_unconfirm'));
    }

    /**
     * Força a remoção de um usuário
     *
     * @param User $user
     *
     * @return User
     * @throws \Exception
     * @throws \Throwable
     * @throws GeneralException
     */
    public function forceDelete(User $user): User
    {
        if ($user->deleted_at === null) {
            throw new GeneralException(__('exceptions.backend.access.users.delete_first'));
        }

        return DB::transaction(function () use ($user) {
            // Delete associated relationships
            $user->passwordHistories()->delete();
            $user->providers()->delete();

            if ($user->forceDelete()) {
                event(new UserPermanentlyDeleted($user));

                return $user;
            }

            throw new GeneralException(__('exceptions.backend.access.users.delete_error'));
        });
    }

    /**
     * Restaura um usuário removido
     *
     * @param User $user
     *
     * @return User
     * @throws GeneralException
     */
    public function restore(User $user): User
    {
        if ($user->deleted_at === null) {
            throw new GeneralException(__('exceptions.backend.access.users.cant_restore'));
        }

        if ($user->restore()) {
            event(new UserRestored($user));

            return $user;
        }

        throw new GeneralException(__('exceptions.backend.access.users.restore_error'));
    }

    /**
     * Verifica se existi o EMAIL informado com OUTRO CPF, caso exista, não permite alterar.
     *
     * @param  User $user
     * @param  string $email
     * @param  string $document
     * @return void
     */
    protected function checkUserByEmail(User $user, $email, $document)
    {
        if ($user->email !== $email && $this->model->where('email', '=', $email)->where('document', '!=', $document)->exists()) {
            throw new GeneralException(trans('exceptions.backend.access.users.email_error'));
        }
    }

    /**
     * Verifica se existe o CPF informado com OUTRO EMAIL, caso exista, não permite alterar.
     *
     * @param  User $user
     * @param  string $email
     * @param  string $document
     * @return void
     */
    protected function checkUserByDocument(User $user, $document, $email)
    {
        if ($user->document !== $document && $this->model->where('document', '=', $document)->where('email', '!=', $email)->exists()) {
            throw new GeneralException(trans('exceptions.backend.access.users.document_error'));
        }
    }

    /**
     * Atualiza o domínio do usuário
     *
     * @param  User $user
     * @param  mixed $dominios
     * @return void
     *
     * @deprecated Futuramente esse método deve ser alterado, para atualizar o "dominio_id" direto da tabela "users"
     */
    public function updateDominios(User $user, $dominios)
    {
        //AQUI
        $user->dominios()->sync($dominios);
    }

    /**
     * Atualiza as unidades operacionais do usuário.
     *
     * Mantém unidades operacionais que o usuário logado não enxerga.
     *
     * Ex:
     * Domínio Ater -> Unidade operacional "Zona Sul" e "Zona Norte"
     * Técnico esta na "Zona Sul" e na "Zona Norte"
     * Unidade Operacional "Zona Norte" só enxerga "Zona Norte"
     * Caso ele atualize o usuário, não pode remover "Zona Sul"
     *
     * @param  User $user
     * @param  mixed $unidadesOperacionais
     * @return void
     */
    public function updateUnidadesOperacionais(User $user, $unidadesOperacionais)
    {
        if (!$unidadesOperacionais) {
            $unidadesOperacionais = [];
        }

        //Unidades Operacionais que o Usuário Logado enxerga
        $listUnidOp = UnidadeOperacionalModel::get()->pluck('id')->toArray();

        //Unidades Operacionais que o Usuário Editado tem
        $listAllUnidOpUser = $user->unidadesOperacionaisNS->pluck('id')->toArray();
        $listAllUnidOpUser = array_diff($listAllUnidOpUser, $listUnidOp);

        //Merge Unid Op. que o usuário não enxerga + Unidades Operacionais que foram salvas no formulário
        $newListUnidOpUser = array_merge($listAllUnidOpUser, $unidadesOperacionais);

        $user->unidadesOperacionais()->sync($newListUnidOpUser);
    }

    /**
     * Cria as roles vinculadas ao usuário.
     *
     * Na prática, ele cria uma cópia do usuário passado com a nova "role".
     *
     * @param  User $user
     * @param  mixed $data
     * @return User
     */
    public function createUserAndRole(User $user, $data): User
    {
        $users = null;

        //Primeiro verifica (de acordo com a "ROLE") se o usuário já existe no sistema, verificando CPF vs Dominio ou CPF vs Unidades Operacionais
        if ($data['role'] == config('access.users.domain_role')) {
            //AQUI
            $users = User::withoutGlobalScopes()->whereHas('dominiosNS', function ($q) use ($data) {
                $q->where('dominios.id', $data['dominio']);
            })->where('document', $user->document);
        } else if ($data['role']  == config('access.users.operational_unit_role') ||  $data['role']  == config('access.users.technician_role')) {
            if (!@$data['unidades_operacionais'] || count($data['unidades_operacionais']) == 0) {
                throw new GeneralException('Selecione ao menos uma unidade operacional.');
            }

            $users = User::withoutGlobalScopes()->whereHas('unidadesOperacionaisNS', function ($q) use ($data) {
                $q->whereIn('unidade_operacionais.id', $data['unidades_operacionais']);
            })->whereHas('roles', function ($q) use ($data) {
                $q->where('name', $data['role']);
            })->where('document', $user->document);
        }

        if ($users && $users->exists()) {
            throw new GeneralException('O usuário já possui essa permissão.');
        }

        $dataUser = array();
        $dataUser['first_name'] = $user->first_name;
        $dataUser['last_name'] = $user->last_name;
        $dataUser['email'] = $user->email;
        $dataUser['document'] = $user->document;
        $dataUser['phone'] = $user->phone;
        $dataUser['address'] = $user->address;
        $dataUser['work'] = $user->work;
        $dataUser['roles'] = [$data['role']];
        $dataUser['password'] = $user->password;
        $dataUser['active'] = "1";

        $userRole = $this->create($dataUser);

        if ($data['role'] == config('access.users.domain_role')) {
            //AQUI
            $this->updateDominios($userRole, [$data['dominio']]);
        }

        if (
            $data['role'] == config('access.users.operational_unit_role') ||
            $data['role'] == config('access.users.technician_role')
        ) {
            $this->updateUnidadesOperacionais($userRole, $data['unidades_operacionais']);
        }

        return $userRole;
    }

    /**
     * Atualiza as roles do usuário.
     *
     * Basicamente atualiza as unidades operacionais, o resto não pode ser atualizado
     *
     * @param  User $userRole
     * @param  mixed $data
     * @return User
     */
    public function updateUserAndRole(User $userRole, $data): User
    {
        if (
            $data['role'] == config('access.users.operational_unit_role') ||
            $data['role'] == config('access.users.technician_role')
        ) {
            if (!@$data['unidades_operacionais'] || count($data['unidades_operacionais']) == 0) {
                if (count($userRole->unidadesOperacionaisNS) <= 1) {
                    throw new GeneralException('Selecione ao menos uma unidade operacional.');
                }
            }

            $this->updateUnidadesOperacionais($userRole, @$data['unidades_operacionais']);
        }

        return $userRole;
    }
}
