<nav id="sidebar" class="c-sidebar c-sidebar-ater c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show">




<div class="c-sidebar-brand">
        <img alt="Logomarca Sisrural" class="c-sidebar-brand-full" src="/img/backend/3-Horta em Casa.jpg" width="180">
        <img  alt="Logomarca Sisrural" class="c-sidebar-brand-minimized" src="/img/backend/3-Horta em Casa.jpg"  width="24" height="26">
    </div>

    <ul class="c-sidebar-nav ps ps--active-y" data-drodpown-accordion="true">
        <ul id="accessibility">
            <li>
                <a accesskey="1" class="fix-anchor" href="#main-content">
                    Ir para o conteúdo <span>1</span>
                </a>
            </li>
            <li>
                <a accesskey="2"  class="fix-anchor open-menu-on-click"  href="#sidebar-anchor">
                    Ir para o menu <span>2</span>
                </a>
            </li>
        </ul>

        <li id="sidebar-anchor" class="c-sidebar-nav-title">
            PRINCIPAL
        </li>

        <li class="c-sidebar-nav-item">
            <a accesskey="i" class="c-sidebar-nav-link {{
                active_class(Route::is('admin/dashboard'))
            }}" href="{{ route('admin.dashboard') }}?origin=sidebar ">
                <i class="c-sidebar-nav-icon c-icon cil-home"></i>
                Página Inicial
            </a>
        </li>

        @cannot('report restricted')
            @canany(['view menu farmers', 'view menu productive units'])
                @canany(['create same operational units farmers', 'create same operational units productive units'])
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link {{
                            active_class(Route::is('admin.core.novo_produtor_unidade_produtiva.*'), 'c-active')
                        }}" href="{{ route('admin.core.novo_produtor_unidade_produtiva.create') }}">
                            <i class="c-sidebar-nav-icon c-icon cil-plus"></i>
                            Novo/a Produtor/a / Unidade Prod.
                        </a>
                    </li>
                @endcanany
            @endcanany

            @can('view menu farmers')
                <li class="c-sidebar-nav-dropdown {{ Route::is('admin.core.produtor.*') ? 'c-show' : '' }}">
                    <a accesskey="p" class="c-sidebar-nav-dropdown-toggle {{ active_class(Route::is('admin.core.produtor.*')) }}" href="#">
                        <i class="c-sidebar-nav-icon c-icon cil-address-book"></i>
                        Produtores/as
                    </a>

                    <ul class="c-sidebar-nav-dropdown-items">
                        <li class="c-sidebar-nav-item">
                            <a class="c-sidebar-nav-link" href="{{ route('admin.core.produtor.index') }}">
                                <i class="c-sidebar-nav-icon c-icon cil-columns"></i>
                                Listar Produtor/as
                            </a>
                         </li>

                         {{-- <li class="c-sidebar-nav-item">
                            <a class="c-sidebar-nav-link" href="{{ route('admin.core.produtor.index_sem_unidade') }}">
                                <i class="c-sidebar-nav-icon c-icon cil-columns"></i>
                                Produtor/a sem unidade
                            </a>
                         </li> --}}

                         <li class="c-sidebar-nav-item">
                            <a class="c-sidebar-nav-link" href="{{ route('admin.core.produtor.create') }}">
                                <i class="c-sidebar-nav-icon c-icon cil-plus"></i>
                                Adicionar Produtor/a
                            </a>
                         </li>
                    </ul>
                </li>
            @endcan

            @can('view menu productive units')
                <li class="c-sidebar-nav-dropdown {{ Route::is('*core.unidade_produtiva*') ? 'c-show' : '' }}">
                    <a accesskey="u" class="c-sidebar-nav-dropdown-toggle {{ active_class(Route::is('*core.unidade_produtiva*'), 'c-active') }}" href="#">
                        <i class="c-sidebar-nav-icon c-icon cil-location-pin"></i>
                        Unidades Produtivas
                    </a>

                    <ul class="c-sidebar-nav-dropdown-items">
                        <li class="c-sidebar-nav-item">
                            <a class="c-sidebar-nav-link" href="{{ route('admin.core.unidade_produtiva.index') }}">
                                <i class="c-sidebar-nav-icon c-icon cil-columns"></i>
                                Listar Unidades Produtivas
                            </a>
                        </li>

                        @can('create same operational units productive units')
                            @php
                                $exist_invalid_unid_produtiva = App\Models\Core\UnidadeProdutivaModel::where("fl_fora_da_abrangencia_app", 1)->first();
                            @endphp

                            @if ($exist_invalid_unid_produtiva)
                                <li class="c-sidebar-nav-item">
                                    <a class="c-sidebar-nav-link" href="{{ route('admin.core.unidade_produtiva.invalid') }}">
                                        <i class="c-sidebar-nav-icon c-icon cil-warning" style="color:red;"></i>
                                        Unidades Prod. Inválidas
                                    </a>
                                </li>
                            @endif

                            <li class="c-sidebar-nav-item">
                                <a class="c-sidebar-nav-link {{
                                    active_class(Route::is('admin.core.unidade_produtiva.create'), 'c-active')
                                }}" href="{{ route('admin.core.unidade_produtiva.produtor') }}">
                                    <i class="c-sidebar-nav-icon c-icon cil-plus"></i>
                                    Adicionar Unidade Produtiva
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('view menu caderno')
                <li class="c-sidebar-nav-dropdown {{ Route::is('*cadernos*') ? 'c-show' : '' }}" >

                    <a accesskey="c" class="c-sidebar-nav-dropdown-toggle {{ active_class(Route::is('admin.core.cadernos*'), 'c-active') }}" href="#">
                        <i class="c-sidebar-nav-icon c-icon cil-clipboard"></i>
                        {{ __('concepts.caderno_de_campo.plural') }}
                    </a>

                    <ul class="c-sidebar-nav-dropdown-items">
                        <li class="c-sidebar-nav-item">
                            <a class="c-sidebar-nav-link" href="{{ route('admin.core.cadernos.index') }}">
                                <i class="c-sidebar-nav-icon c-icon cil-columns"></i>
                                {{ __('concepts.caderno_de_campo.list') }}
                            </a>
                        </li>

                        @can('create caderno')
                        <li class="c-sidebar-nav-item">
                            <a class="c-sidebar-nav-link {{
                                active_class(Route::is('admin.core.cadernos.create'), 'c-active')
                            }}" href="{{ route('admin.core.cadernos.produtor_unidade_produtiva') }}">
                                <i class="c-sidebar-nav-icon c-icon cil-plus"></i>
                                {{ __('concepts.caderno_de_campo.add') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
            @endcan



            @can('view menu checklist_unidade_produtiva')
                <li class="c-sidebar-nav-dropdown {{ Route::is('admin.core.checklist_unidade_produtiva.*') ? 'c-show' : '' }}">
                    <a accesskey="f" class="c-sidebar-nav-dropdown-toggle {{ active_class(Route::is('admin.core.checklist_unidade_produtiva.*'), 'c-active') }}" href="#">
                        <i class="c-sidebar-nav-icon c-icon cil-clipboard"></i>
                        Formulário
                    </a>

                    <ul class="c-sidebar-nav-dropdown-items">
                        <li class="c-sidebar-nav-item">
                            <a class="c-sidebar-nav-link" href="{{ route('admin.core.checklist_unidade_produtiva.index') }}">
                                <i class="c-sidebar-nav-icon c-icon cil-columns"></i>
                                Listar Formulários
                            </a>
                        </li>

                        <li class="c-sidebar-nav-item">
                            <a class="c-sidebar-nav-link" href="{{ route('admin.core.checklist_unidade_produtiva.analiseIndex') }}">
                                <i class="c-sidebar-nav-icon c-icon cil-columns"></i>
                                Analisar Formulários
                            </a>
                        </li>

                        @can('create checklist_unidade_produtiva')
                            <li class="c-sidebar-nav-item">
                                <a class="c-sidebar-nav-link {{
                                    active_class(Route::is('admin.core.checklist_unidade_produtiva.create') || Route::is('admin.core.checklist_unidade_produtiva.produtor_unidade_produtiva'), 'c-active')
                                }}" href="{{ route('admin.core.checklist_unidade_produtiva.template') }}">
                                    <i class="c-sidebar-nav-icon c-icon cil-plus"></i>
                                    Aplicar Formulário
                                </a>
                            </li>

                            <li class="c-sidebar-nav-item">
                                <a class="c-sidebar-nav-link {{
                                    active_class(Route::is('admin.core.checklist_unidade_produtiva.compare'), 'c-active')
                                }}" href="{{ route('admin.core.checklist_unidade_produtiva.compare') }}">
                                    <i class="c-sidebar-nav-icon c-icon cil-clone"></i>
                                    Comparar Formulário
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('view menu plano_acao')
                <li class="c-sidebar-nav-dropdown {{ Route::is('admin.core.plano_acao.*') ? 'c-show' : '' }}">
                    <a  class="c-sidebar-nav-dropdown-toggle {{ active_class(Route::is('admin.core.plano_acao.*'), 'c-active') }}" href="#">
                        <i class="c-sidebar-nav-icon c-icon cil-clipboard"></i>
                        Plano de Ação
                    </a>

                    <ul class="c-sidebar-nav-dropdown-items">
                        <li class="c-sidebar-nav-item">
                            <a class="c-sidebar-nav-link" href="{{ route('admin.core.plano_acao.index') }}">
                                <i class="c-sidebar-nav-icon c-icon cil-columns"></i>
                                Listar Planos de Ação
                            </a>
                        </li>

                        @can('create plano_acao')
                            <li class="c-sidebar-nav-item">
                                <a class="c-sidebar-nav-link {{
                                    active_class(Route::is('admin.core.plano_acao.create') || Route::is('admin.core.plano_acao.produtor_unidade_produtiva'), 'c-active')
                                }}" href="{{ route('admin.core.plano_acao.produtor_unidade_produtiva') }}">
                                    <i class="c-sidebar-nav-icon c-icon cil-plus"></i>
                                    Adicionar Plano - Individual
                                </a>
                            </li>

                            <li class="c-sidebar-nav-item">
                                <a class="c-sidebar-nav-link {{
                                    active_class(Route::is('admin.core.plano_acao.create_com_checklist'), 'c-active')
                                }}" href="{{ route('admin.core.plano_acao.checklist_unidade_produtiva') }}">
                                    <i class="c-sidebar-nav-icon c-icon cil-plus"></i>
                                    Adicionar Plano - Formulário
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('view menu plano_acao')
                <li class="c-sidebar-nav-dropdown {{ Route::is('admin.core.plano_acao_coletivo.*') ? 'c-show' : '' }}">
                    <a class="c-sidebar-nav-dropdown-toggle {{ active_class(Route::is('admin.core.plano_acao_coletivo.*'), 'c-active') }}" href="#">
                        <i class="c-sidebar-nav-icon c-icon cil-clipboard"></i>
                        Plano de Ação Coletivo
                    </a>

                    <ul class="c-sidebar-nav-dropdown-items">
                        <li class="c-sidebar-nav-item">
                            <a class="c-sidebar-nav-link" href="{{ route('admin.core.plano_acao_coletivo.index') }}">
                                <i class="c-sidebar-nav-icon c-icon cil-columns"></i>
                                Listar Planos de Ação
                            </a>
                        </li>

                        @can('create plano_acao')
                            <li class="c-sidebar-nav-item">
                                <a class="c-sidebar-nav-link {{
                                    active_class(Route::is('admin.core.plano_acao_coletivo.create'), 'c-active')
                                }}" href="{{ route('admin.core.plano_acao_coletivo.create') }}">
                                    <i class="c-sidebar-nav-icon c-icon cil-plus"></i>
                                    Adicionar Plano de Ação
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
        @endcannot

        @can('view menu report')
            @can('report restricted')
                <li class="c-sidebar-nav-title">
                    CONFIGURAÇÕES
                </li>

                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.auth.user.*'), 'c-active')
                    }}" href="{{ route('admin.auth.user.index') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-people"></i>
                        Usuários/as
                    </a>
                </li>
            @endcan

            <li class="c-sidebar-nav-title">
                RELATÓRIOS
            </li>

            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{
                    active_class(Route::is('admin.core.mapa.*'), 'c-active')
                }}" href="{{ route('admin.core.mapa.index', ['dt_ini'=>date('Y-m-d', strtotime("-1 year")), 'dt_end'=>date('Y-m-d')]) }}">
                    <i class="c-sidebar-nav-icon c-icon cil-description"></i>
                    Mapa
                </a>
            </li>

            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{
                    active_class(Route::is('admin.core.report.index'), 'c-active')
                }}" href="{{ route('admin.core.report.index') }}">
                    <i class="c-sidebar-nav-icon c-icon cil-description"></i>
                    Download CSV
                </a>
            </li>

            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{
                    active_class(Route::is('admin.core.indicadores.index'), 'c-active')
                }}" href="{{ route('admin.core.indicadores.index') }}">
                    <i class="c-sidebar-nav-icon c-icon cil-description"></i>
                    Indicadores
                </a>
            </li>

           <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{
                    active_class(Route::is('admin.core.logs.index'), 'c-active')
                }}" href="{{ route('admin.core.logs.index') }}">
                    <i class="c-sidebar-nav-icon c-icon cil-description"></i>
                    Logs
                </a>
            </li>
        @endcan

        @cannot('report restricted')
            <li class="c-sidebar-nav-title">
                CONFIGURAÇÕES
            </li>

            @can('view menu domains')
                <li class="c-sidebar-nav-item">
                    <a   class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.core.dominio.*'), 'c-active')
                    }}" href="{{ route('admin.core.dominio.index') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-layers"></i>
                        Domínios
                    </a>
                </li>
            @endcan

            @can('view menu operational units')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.core.unidade_operacional.*'), 'c-active')
                    }}" href="{{ route('admin.core.unidade_operacional.index') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-compass"></i>
                        Unidades Operacionais
                    </a>
                </li>
            @endcan

            @can('view menu regions')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.core.regiao.*'), 'c-active')
                    }}" href="{{ route('admin.core.regiao.index') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-map"></i>
                        Regiões
                    </a>
                </li>
            @endcan

            @adminLOP
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.core.solo_categoria.*'), 'c-active')
                    }}" href="{{ route('admin.core.solo_categoria.index') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-compass"></i>
                        Uso do Solo - Categorias
                    </a>
                </li>
            @endadminLOP

            @can('view menu users')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.auth.user.*'), 'c-active')
                    }}" href="{{ route('admin.auth.user.index') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-people"></i>
                        Usuários/as
                    </a>
                </li>
            @endcan

            {{-- @can('view menu roles')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.auth.role.*'), 'c-active')
                    }}" href="{{ route('admin.auth.role.index') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-settings"></i>
                        Permissões
                    </a>
                </li>
            @endcan --}}

            @canany(['view menu caderno base', 'view menu questao', 'view menu checklist base',
                     'view menu categoria checklist', 'view menu pergunta checklist'])
                <li class="c-sidebar-nav-title">
                    TEMPLATES
                </li>

                @can('view menu caderno base')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link {{
                            active_class(Route::is('admin.core.templates_caderno.*'), 'c-active')
                        }}" href="{{ route('admin.core.templates_caderno.index') }}">
                            <i class="c-sidebar-nav-icon c-icon cil-description"></i>
                            Caderno de Campo Base
                            </a>
                    </li>
                @endcan
                @can('view menu questao')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link {{
                            active_class(Route::is('admin.core.template_perguntas.*'), 'c-active')
                        }}" href="{{ route('admin.core.template_perguntas.index') }}">
                            <i class="c-sidebar-nav-icon c-icon cil-speech"></i>
                            Perguntas p/ Cad. de Campo
                        </a>
                    </li>
                @endcan
                @can('view menu checklist base')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link {{
                            active_class(Route::is('admin/checklist*'))
                        }}" href="{{ route('admin.core.checklist.index') }}">
                            <i class="c-sidebar-nav-icon c-icon cil-description"></i>
                            Formulários para Aplicação
                        </a>
                    </li>
                @endcan
                @can('view menu pergunta checklist')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link {{
                            active_class(Route::is('admin/perguntas*'))
                        }}" href="{{ route('admin.core.perguntas.index') }}">
                            <i class="c-sidebar-nav-icon c-icon cil-speech"></i>
                            Perguntas para Formulários
                        </a>
                    </li>
                @endcan
                @can('view menu checklist base')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link {{
                            active_class(Route::is('admin/checklist/biblioteca'))
                        }}" href="{{ route('admin.core.checklist.biblioteca') }}">
                            <i class="c-sidebar-nav-icon c-icon cil-description"></i>
                            Biblioteca de Formulários
                        </a>
                    </li>
                @endcan
            @endcanany

            <li class="c-sidebar-nav-title">
                AJUDA
            </li>

            {{-- <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{
                    active_class(Route::is('admin.core.sobre.*'), 'c-active')
                }}" href="{{ route('admin.core.sobre.index') }}">
                    <i class="c-sidebar-nav-icon c-icon cil-description"></i>
                    Sobre
                </a>
            </li> --}}

            @can('view menu termos')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.core.termos_de_uso.*'), 'c-active')
                    }}" href="{{ route('admin.core.termos_de_uso.index') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-shield-alt"></i>
                        Termo de Uso
                    </a>
                </li>
            @endcan

            @adminLOP
                <li class="c-sidebar-nav-title">
                    API
                </li>

                {{-- <li class="c-sidebar-nav-dropdown {{ Route::is('*core.dado*') ? 'c-show' : '' }}">
                    <a class="c-sidebar-nav-dropdown-toggle {{ active_class(Route::is('*core.dado*'), 'c-active') }}" href="#">
                        <i class="c-sidebar-nav-icon c-icon cil-location-pin"></i>
                        Sampa+Rural
                    </a>

                    <ul class="c-sidebar-nav-dropdown-items"> --}}
                        <li class="c-sidebar-nav-item">
                            <a class="c-sidebar-nav-link" href="{{ route('admin.core.dado.index') }}">
                                <i class="c-sidebar-nav-icon c-icon cil-columns"></i>
                                Sampa+Rural
                            </a>
                        </li>

                        @can('create', App\Models\Core\DadoModel::class)
                            <li class="c-sidebar-nav-item">
                                <a class="c-sidebar-nav-link {{
                                    active_class(Route::is('admin.core.dado.create'), 'c-active')
                                }}" href="{{ route('admin.core.dado.create') }}">
                                    <i class="c-sidebar-nav-icon c-icon cil-plus"></i>
                                    Criar acesso Sampa+Rural
                                </a>
                            </li>
                        @endcan
                    {{-- </ul>
                </li> --}}
            @endadminLOP

            @admin
                <li class="c-sidebar-nav-title">
                    IMPORTADOR
                </li>

                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.core.importador.*'), 'c-active')
                    }}" href="{{ route('admin.core.importador.create') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-shield-alt"></i>
                        Importar Produtores/as
                    </a>

                   <a class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.core.importador.*'), 'c-active')
                    }}" href="{{ route('admin.core.importador.editProdutor') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-shield-alt"></i>
                        Atualizar Produtores/as (Alguns Dados)
                    </a>

                    <a class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.core.importador.createCaderno'), 'c-active')
                    }}" href="{{ route('admin.core.importador.createCaderno') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-shield-alt"></i>
                        Importar Caderno Campo
                    </a>

                    <a class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.core.importador.createChecklistUnidadeProdutiva'), 'c-active')
                    }}" href="{{ route('admin.core.importador.createChecklistUnidadeProdutiva') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-shield-alt"></i>
                        Importar Formulários
                    </a>

                    <a class="c-sidebar-nav-link {{
                        active_class(Route::is('admin.core.importador.createUsuarios'), 'c-active')
                    }}" href="{{ route('admin.core.importador.createUsuarios') }}">
                        <i class="c-sidebar-nav-icon c-icon cil-shield-alt"></i>
                        Importar Usuários/as
                    </a>
                </li>
            @endadmin
        @endcannot
    </ul>

    <button aria-label="Minimizar / Maximizar menu" class="c-sidebar-minimizer c-class-toggler" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show"></button>
</nav>
