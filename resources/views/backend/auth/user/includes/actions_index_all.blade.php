@can('createRole', App\Models\Auth\User::class)
    <div class="btn-group" role="group" aria-label="@lang('labels.backend.access.users.user_actions')">
        <a href="{{ route('admin.auth.user.roles.create.userAll', ['userAll'=>$user]) }}" data-toggle="tooltip" data-placement="top" title="Adicionar Habilidade" class="btn btn-primary">
            <i class="fa fa-plus"></i>
        </a>
    </div>
@endcan
