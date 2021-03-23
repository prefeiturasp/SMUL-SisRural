<header class="c-header c-header-light c-header-fixed c-header-with-subheader">




<button aria-label="Esconder ou Mostrar o menu" class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true">
        <span class="c-header-toggler-icon"></span>
    </button>

    <button aria-label="Esconder ou Mostrar o menu" class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show">
        <span class="c-header-toggler-icon"></span>
    </button>

    <ul class="c-header-nav mfs-auto"></ul>

    <div class="c-header-nav">
            @if (count($logged_in_user->allRoles()) > 1)
                <div class="btn-group btn-group-sm mr-4" role="group">
                    <button id="userRoles" type="button" class="btn btn-secondary dropdown-toggle btn-dropdown-secondary" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        {{@$logged_in_user->getDominioRoleAttribute()}}
                    </button>

                    <div class="dropdown-menu dropdown-menu-left dropdown-menu-roles" aria-labelledby="userRoles">
                        @foreach($logged_in_user->allRoles() as $role)
                            <a href="{{ route('admin.auth.user.change_role', [ 'userId'=> $role['id'] ]) }}"
                                    data-method="post"
                                    data-trans-button-cancel="Cancelar"
                                    data-trans-button-confirm="Confirmar"
                                    data-trans-title="Deseja trocar de papel?"
                                    class="dropdown-item {{ $role['id'] == session('auth_user_id') ? 'font-weight-bold' : null }}">
                                        {{$role['nome']}}
                                </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="c-avatar mr-2">
                <span>{{substr($logged_in_user->first_name, 0, 1).substr($logged_in_user->last_name, 0, 1)}}</span>
            </div>

            <span class="d-md-down-none">{{ $logged_in_user->full_name }}</span>

            <span class="separator ml-4 mr-4"></span>

            <a class="mr-4 logout-button" href="{{ route('frontend.auth.logout') }}">
                    @lang('navs.general.logout')
            </a>
    </div>

    <div class="c-subheader justify-content-between px-3">
        {!! Breadcrumbs::render() !!}
    </div>
</header>
