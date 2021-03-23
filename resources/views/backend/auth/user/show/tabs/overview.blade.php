<div class="col">
    <div class="table-responsive">
        <table class="table table-hover">
            {{-- <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.avatar')</th>
                <td><img src="{{ $user->picture }}" class="user-profile-image" /></td>
            </tr> --}}

            <tr>
                <th>Papel</th>
                <td>{{ $user->getRolesLabelAttribute() }}</td>
            </tr>

            {{-- Usuário do tipo Domínio --}}
            @if (count($user->dominios) > 0)
                <tr>
                    <th>Domínio</th>
                    {{-- //AQUI --}}
                    <td>{{ join(", ", $user->dominios->pluck('nome')->toArray()) }}</td>
                </tr>
            @endif

            {{-- Usuário do tipo Unidade Operacional ou Técnico --}}
            @if (@count($user->unidadesOperacionais) > 0)
                <tr>
                    <th>Domínio</th>
                    <td>{{ join(", ", $user->unidadesOperacionais()->with('dominio')->get()->pluck('dominio.nome')->toArray()) }}</td>
                </tr>

                <tr>
                    <th>Unidade Operacionais</th>
                    <td>{{ join(", ", $user->unidadesOperacionais->pluck('nome')->toArray()) }}</td>
                </tr>
            @endif

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.name')</th>
                <td>{{ $user->name }}</td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.email')</th>
                <td>{{ $user->email }}</td>
            </tr>

            <tr>
                <th><abbr title="Cadastro de Pessoa Física">CPF</abbr></th>
                <td>{{ App\Helpers\General\AppHelper::formatCpfCnpj($user->document) }}</td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.status')</th>
                <td>@include('backend.auth.user.includes.status', ['user' => $user])</td>
            </tr>

            <tr>
                <th>Telefone</th>
                <td>{{ $user->phone }}</td>
            </tr>

            <tr>
                <th>Endereço</th>
                <td>{{ $user->address }}</td>
            </tr>

            <tr>
                <th>Onde Trabalha?</th>
                <td>{{ $user->work }}</td>
            </tr>

            {{-- <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.confirmed')</th>
                <td>@include('backend.auth.user.includes.confirm', ['user' => $user])</td>
            </tr> --}}

            {{-- <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.timezone')</th>
                <td>{{ $user->timezone }}</td>
            </tr> --}}

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.last_login_at')</th>
                <td>
                    @if($user->last_login_at)
                        {{ timezone()->convertToLocal($user->last_login_at, 'd/m/Y H:i:s') }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>

            <tr>
                <th>@lang('labels.backend.access.users.tabs.content.overview.last_login_ip')</th>
                <td>{{ $user->last_login_ip ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>
</div><!--table-responsive-->

@if (@count($user->dominios) > 0 || @count($user->unidadesOperacionais) > 0)
    @cardater(['title' => 'Abrangências', 'class'=>'card-custom-border'])
        @slot('body')
            @foreach ($user->dominios as $k=>$v)
                @cardater(['title' => 'Domínio - '.$v->nome, 'class'=>'card-custom-border'])
                    @slot('body')
                        <table class="table table-hover">
                            <tr>
                                <td width="44%">Abrangência Estadual</td>
                                <td>{{ join(", ", $v->abrangenciaEstadual->pluck('nome')->toArray()) }}</td>
                            </tr>
                            <tr>
                                <td>Abrangência Municipal</td>
                                <td>{{ join(", ", $v->abrangenciaMunicipal->pluck('nome')->toArray()) }}</td>
                            </tr>
                            <tr>
                                <td>Abrangência Regional</td>
                                <td>{{ join(", ", $v->abrangenciaRegional->pluck('nome')->toArray()) }}</td>
                            </tr>
                        </table>
                    @endslot
                @endcardater
            @endforeach


            @foreach ($user->unidadesOperacionais as $k=>$v)
                @cardater(['title' => 'Unidade Operacional - '.$v->nome, 'class'=>'card-custom-border'])
                    @slot('body')
                        <table class="table table-hover">
                            <tr>
                                <td  width="44%">Abrangência Estadual</td>
                                <td>{{ join(", ", $v->abrangenciaEstadual->pluck('nome')->toArray()) }}</td>
                            </tr>
                            <tr>
                                <td>Abrangência Municipal</td>
                                <td>{{ join(", ", $v->abrangenciaMunicipal->pluck('nome')->toArray()) }}</td>
                            </tr>
                            <tr>
                                <td>Abrangência Regional</td>
                                <td>{{ join(", ", $v->regioes->pluck('nome')->toArray()) }}</td>
                            </tr>
                        </table>
                    @endslot
                @endcardater
            @endforeach
        @endslot
    @endcardater
@endif
