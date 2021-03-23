<?php

namespace App\Services;

use App\Enums\CadernoStatusEnum;
use App\Enums\ChecklistStatusEnum;
use App\Enums\ProdutorUnidadeProdutivaStatusEnum;
use App\Helpers\General\AppHelper;
use App\Models\Auth\User;
use App\Models\Core\CadernoModel;
use App\Models\Core\CertificacaoModel;
use App\Models\Core\ChecklistModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\DominioModel;
use App\Models\Core\EstadoModel;
use App\Models\Core\GeneroModel;
use App\Models\Core\SoloCategoriaModel;
use App\Models\Core\TemplateModel;
use App\Models\Core\UnidadeOperacionalModel;
use App\Models\Core\UnidadeProdutivaModel;
use App\Repositories\Backend\Core\ChecklistRepository;
use App\Repositories\Backend\Core\DominioRepository;
use App\Repositories\Backend\Core\RegiaoRepository;
use App\Repositories\Backend\Core\UnidadeOperacionalRepository;
use DateTime;
use \Illuminate\Database\Eloquent\Builder;

class ReportService
{
    public function __construct(DominioRepository $dominioRepository, UnidadeOperacionalRepository $unidadeOperacionalRepository, RegiaoRepository $regiaoRepository, ChecklistRepository $checklistRepository)
    {
        $this->dominioRepository = $dominioRepository;
        $this->unidadeOperacionalRepository = $unidadeOperacionalRepository;
        $this->regiaoRepository = $regiaoRepository;
        $this->checklistRepository = $checklistRepository;
    }

    /**
     * Dados privados
     *
     * Cliente pediu para retirar os dados privados. Iremos deixar comentado o código caso volte.
     */
    public function isPrivateData()
    {
        if (\Auth::user()->can('report restricted')) {
            return true;
        }

        return false;
    }

    public function getFilterData($request)
    {
        return $request->only(
            [
                'dt_ini', 'dt_end', 'dominio_id', 'unidade_operacional_id', 'estado_id', 'cidade_id', 'regiao_id', 'produtor_id', 'unidade_produtiva_id',
                'atuacao_dominio_id', 'atuacao_unidade_operacional_id', 'atuacao_tecnico_id',
                'checklist_id', 'certificacao_id', 'solo_categoria_id', 'area', 'genero_id', 'status_unidade_produtiva', 'template_caderno_id',
                'pergunta_id',

                'filter_checklist_id'
            ]
        );
    }

    public function rangeDates($dtIni, $dtEnd) {
        $start = strtotime($dtIni);
        $end = strtotime($dtEnd);
        $ret = [];

        while($start <= $end)
        {
            $ret[] = ['date' => date('m/Y', $start), 'date_sort' => date('Ym', $start)];
            $start = strtotime("+1 month", $start);
        }

        return $ret;
    }

    private function getAbrangenciaTxt()
    {
        $unidadesOperacionaisAbrangencias = UnidadeOperacionalModel::with(
            [
                'regioes:regioes.id,regioes.nome',
                'abrangenciaMunicipal:cidades.id,cidades.nome,cidades.estado_id',
                'abrangenciaEstadual:estados.id,estados.nome,estados.uf'
            ]
        )->get();

        $estados = $unidadesOperacionaisAbrangencias
            ->pluck('abrangenciaEstadual')
            ->collapse()
            ->pluck('nome')
            ->toArray();

        $estadosIds =  $unidadesOperacionaisAbrangencias
            ->pluck('abrangenciaEstadual')
            ->collapse()
            ->pluck('id')
            ->unique()
            ->toArray();

        $cidades = $unidadesOperacionaisAbrangencias
            ->pluck('abrangenciaMunicipal')
            ->collapse()
            ->whereNotIn('estado_id', $estadosIds)
            ->pluck('nome')
            ->toArray();

        $regioes = $unidadesOperacionaisAbrangencias
            ->pluck('regioes')
            ->collapse()
            ->pluck('nome')
            ->toArray();

        $value = join(
            ", ",
            array_unique(
                array_merge($estados, $cidades, $regioes)
            )
        );

        return $value;
    }

    public function viewFilter(?string $dataUrl, bool $expand = true, bool $checkTerms = true, $requiredDate = false, $bi=false)
    {
        $ids = ChecklistUnidadeProdutivaModel::select('checklist_id')->distinct()->pluck('checklist_id')->toArray();
        $checklistsAll = ChecklistModel::get(['id', 'nome'])->pluck('nome', 'id')->toArray() + ChecklistModel::withoutGlobalScopes()->whereIn('id', $ids)->get(['id', 'nome'])->pluck('nome', 'id')->toArray();

        $idsCadernos = CadernoModel::where('status', CadernoStatusEnum::Finalizado)->select('template_id')->distinct()->pluck('template_id')->toArray();
        $templateCadernos = TemplateModel::withoutGlobalScopes()
            ->with(['dominio'])
            ->whereIn('id', $idsCadernos)
            ->get(['id', 'nome', 'dominio_id']);

        //Abrangencia
        $abrangenciasTxt = '';
        if (!\Auth::user()->isAdmin() && !\Auth::user()->isAdminLOP()) {
            $abrangenciasTxt = $this->getAbrangenciaTxt();
        }

        return view('backend.core.report.filter.filter', compact('dataUrl', 'expand', 'checkTerms', 'abrangenciasTxt', 'requiredDate', 'bi'))
            ->withDominios($this->dominioRepository->get(['id', 'nome']))
            ->withUnidadeOperacionais($this->unidadeOperacionalRepository->get(['id', 'nome']))
            ->withEstados(EstadoModel::orderBy('nome')->get(['id', 'nome']))
            ->withRegioes($this->regiaoRepository->get(['id', 'nome']))
            ->withChecklists($checklistsAll)
            ->withCertificacoes(CertificacaoModel::get(['id', 'nome']))
            ->withSoloCategorias(SoloCategoriaModel::get(['id', 'nome']))
            ->withArea(['0_0.5' => 'Até 0,5 Ha', '0.5_1' => 'De 0,5 a 1 Ha', '1_5' => 'De 1 a 5 Ha', '5_10' => 'De 5 a 10 Ha', '10_20' => 'De 10 a 20 Ha', '20_50' => 'De 20 a 50 ha', '50_>' => 'Acima de 50 ha'])
            ->withGeneros(GeneroModel::get(['id', 'nome']))
            ->withTemplateCaderno($templateCadernos)
            ->withStatusUnidadeProdutiva(ProdutorUnidadeProdutivaStatusEnum::toSelectArray())
            ->withAtuacaoDominios(DominioModel::get(['id', 'nome'])->merge(DominioModel::withoutGlobalScopes()->get(['id', 'nome'])))
            ->withAtuacaoUnidadeOperacionais(UnidadeOperacionalModel::get(['id', 'nome'])->merge(UnidadeOperacionalModel::withoutGlobalScopes()->get(['id', 'nome'])))
            ->withAtuacaoTecnicos(
                User::whereHas('roles', function ($q) {
                    $q->where('name', 'Tecnico');
                    $q->orWhere('name', 'Unidade Operacional');
                })
                    ->get(['id', 'first_name', 'last_name'])
                    ->sortBy('full_name_dominio_role')
                    ->merge(
                        User::withoutGlobalScopes()
                            ->whereHas('roles', function ($q) {
                                $q->where('name', 'Tecnico');
                                $q->orWhere('name', 'Unidade Operacional');
                            })->get(['id', 'first_name', 'last_name'])->sortBy('full_name_dominio_role')
                    )
                    ->pluck('full_name_dominio_role', 'id')
            );
    }


    /**
     *
     * Query - Formulários aplicados nas unidades produtivas
     *
     * Formulários FINALIZADOS
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     * @param string $dt_ini
     * @param string $dt_end
     */
    public function queryChecklists(Builder $query, string $alias, ?array $values, ?string $dt_ini = '', ?string $dt_end = '')
    {
        if (!$values || count($values) == 0) {
            return;
        }

        $query->whereHas($alias, function ($q) use ($values, $dt_ini, $dt_end) {
            $q->whereIn('checklist_unidade_produtivas.checklist_id', $values);
            $q->where('checklist_unidade_produtivas.status', ChecklistStatusEnum::Finalizado);

            if (@$dt_ini && @$dt_end) {
                $q->whereBetween('checklist_unidade_produtivas.finished_at', $this->dateBetween($dt_ini, $dt_end));
            }
        });
    }

    /**
     *
     * Query - Formulários aplicados nas unidades produtivas independente do status
     *
     * Utilizado apenas no report do formulário
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     * @param string $dt_ini
     * @param string $dt_end
     */
    public function queryChecklistsAllStatus(Builder $query, string $alias, ?array $values, ?string $dt_ini = '', ?string $dt_end = '')
    {
        if (!$values || count($values) == 0) {
            return;
        }

        $query->whereHas($alias, function ($q) use ($values, $dt_ini, $dt_end) {
            $q->whereIn('checklist_unidade_produtivas.checklist_id', $values);
            // $q->where('checklist_unidade_produtivas.status', ChecklistStatusEnum::Finalizado);

            if (@$dt_ini && @$dt_end) {
                $q->where(function ($qq) use ($dt_ini, $dt_end) {
                    $qq->whereBetween('checklist_unidade_produtivas.updated_at', $this->dateBetween($dt_ini, $dt_end));
                    $qq->orWhereBetween('checklist_unidade_produtivas.finished_at', $this->dateBetween($dt_ini, $dt_end));
                });
            }
        });
    }


    /**
     *
     * Query - Certificações das unidades produtivas
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     */
    public function queryCertificacoes(Builder $query, ?string $aliasUnidadeProd, string $alias, ?array $values)
    {
        if (!$values || count($values) == 0) {
            return;
        }

        if ($aliasUnidadeProd) {
            $query->whereHas($aliasUnidadeProd, function ($q) use ($alias, $values) {
                $q->whereHas($alias, function ($qq) use ($values) {
                    $qq->whereIn('certificacoes.id', $values);
                });

                if (array_search("0", $values) !== FALSE) {
                    $q->orWhere('fl_certificacoes', 0); //Não possui
                    $q->orWhereNull('fl_certificacoes'); //Não respondeu
                }
            });
        } else {
            $query->where(function ($q) use ($alias, $values) {
                $q->whereHas($alias, function ($qq) use ($values) {
                    $qq->whereIn('certificacoes.id', $values);
                });

                if (array_search("0", $values) !== FALSE) {
                    $q->orWhere('unidade_produtivas.fl_certificacoes', 0); //Não possui
                    $q->orWhereNull('unidade_produtivas.fl_certificacoes');  //Não respondeu
                }
            });
        }
    }

    /**
     *
     * Query - Uso do Solo das unidades produtivas (Uso do Solo e Caracterizacao (é um tipo de uso do solo em outra tabela))
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     */
    public function querySoloCategoria(Builder $query, string $alias, string $aliasCaracterizacao, ?array $values)
    {
        if (!$values || count($values) == 0) {
            return;
        }

        $query->where(function ($qq) use ($alias, $aliasCaracterizacao, $values) {
            $qq->whereHas($alias, function ($q) use ($values) {
                $q->whereIn('solo_categorias.id', $values);
            });

            $qq->orWhereHas($aliasCaracterizacao, function ($q) use ($values) {
                $q->whereIn('unidade_produtiva_caracterizacoes.solo_categoria_id', $values);
            });
        });
    }

    /**
     *
     * Query - Área total das unidades produtivas
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     *
     * [
     *  '0_0.5' => 'Até 0,5 Ha',
     *  '0.5_1' => 'De 0,5 a 1 Ha',
     *  '1_5' => 'De 1 a 5 Ha',
     *  '5_10' => 'De 5 a 10 Ha',
     *  '10_20' => 'De 10 a 20 Ha',
     *  '20_50' => 'De 20 a 50 ha',
     *  '50_>' => 'Acima de 50 ha'
     * ]
     *
     */
    public function queryArea(Builder $query, ?string $alias, ?array $values)
    {
        if (!$values || count($values) == 0) {
            return;
        }

        if ($alias) {
            $query->whereHas($alias, function ($q) use ($values) {
                $q->whereRaw('1 != 1');
                foreach ($values as $v) {
                    $this->_queryArea($q, $v);
                }
            });
        } else {
            $query->where(function ($q) use ($values) {
                $q->whereRaw('1 != 1');
                foreach ($values as $v) {
                    $this->_queryArea($q, $v);
                }
            });
        }
    }
    private function _queryArea($q, $v)
    {
        $values = explode("_", $v);

        if (is_numeric($values[1])) {
            $q->orWhereBetween('unidade_produtivas.area_total_solo', [$values[0] * 1, $values[1] * 1]);
        } else {
            $q->orWhere('unidade_produtivas.area_total_solo', '>=', $values[0] * 1);
        }
    }

    /**
     *
     * Query - Gênero dos produtores
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     */
    public function queryGenero(Builder $query, ?string $alias, ?array $values)
    {
        if (!$values || count($values) == 0) {
            return;
        }

        if (@$alias) {
            $query->whereHas($alias, function ($q) use ($values) {
                $q->whereIn('produtores.genero_id', $values);
            });
        } else {
            $query->whereIn('produtores.genero_id', $values);
        }
    }

    /**
     *
     * Query - Status das unidades produtivas
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     */
    public function queryStatusUnidadeProdutiva(Builder $query, ?string $alias, ?array $values)
    {
        if (!$values || count($values) == 0) {
            return;
        }

        if ($alias) {
            $query->whereHas($alias, function ($q) use ($values) {
                $q->whereIn('unidade_produtivas.status', $values);
            });
        } else {
            $query->whereIn('unidade_produtivas.status', $values);
        }
    }

    /**
     *
     * Query - Domínio das unidades produtivas
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     */
    public function queryDominios(Builder $query, ?string $alias, ?array $values)
    {
        if (!$values || count($values) == 0) {
            return;
        }

        $unidadesOperacionais = UnidadeOperacionalModel::withoutGlobalScopes()
            ->whereIn('dominio_id', $values)
            ->pluck('id');

        $query->orWhereHas($alias, function ($q) use ($unidadesOperacionais) {
            $q->whereIn('unidade_operacional_unidade_produtiva.unidade_operacional_id', $unidadesOperacionais);
        });
    }


    /**
     *
     * Query - Unidades operacionais das unidades produtivas
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     */
    public function queryUnidadesOperacionais(Builder $query, ?string $alias, ?array $values)
    {
        if (!$values || count($values) == 0) {
            return;
        }

        $query->orWhereHas($alias, function ($q) use ($values) {
            $q->whereIn('unidade_operacional_unidade_produtiva.unidade_operacional_id', $values);
        });
    }

    /**
     *
     * Query - Estado da unidade produtiva
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     */
    public function queryEstados(Builder $query, ?string $alias, ?array $values)
    {
        if (!$values || count($values) == 0) {
            return;
        }

        if ($alias) {
            $query->orWhereHas($alias, function ($q) use ($values) {
                $q->whereIn('unidade_produtivas.estado_id', $values);
            });
        } else {
            $query->orWhereIn('unidade_produtivas.estado_id', $values);
        }
    }

    /**
     *
     * Query - Cidade da unidade produtiva
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     */
    public function queryCidades(Builder $query, ?string $alias, ?array $values)
    {
        if (!$values || count($values) == 0) {
            return;
        }

        if ($alias) {
            $query->orWhereHas($alias, function ($q) use ($values) {
                $q->whereIn('unidade_produtivas.cidade_id', $values);
            });
        } else {
            $query->orWhereIn('unidade_produtivas.cidade_id', $values);
        }
    }

    /**
     * Query para retornar unidades produtivas que fazem parte de uma região
     *
     * (select id, uid, lat, lng, nome, st_contains(ST_GeomFromText("GEOMETRYCOLLECTION(POLYGON((-51.2719278394267 -29.08350969415044,-51.2774210034892 -29.217837111866125,-51.08241367927045 -29.227425202415954,-51.06593418708295 -29.099110276819946,-51.2719278394267 -29.08350969415044)))"), POINT(lng,lat)) as status
     */
    public function queryRegioes(Builder $query, ?string $alias, ?array $values)
    {
        if (!$values || count($values) == 0 || $values[0] == null) {
            return;
        }

        $regioesUnidProd = UnidadeProdutivaModel::query();

        foreach ($values as $v) {
            $regioesUnidProd->orWhereRaw(
                'st_contains(
                (select poligono from regioes where id = ?), POINT(lng, lat)
            ) = 1',
                $v
            );
        }

        $unidadesProdutivas = $regioesUnidProd->get(['id']);

        // Implementação antiga
        // $unidadesProdutivas = array();
        // foreach (UnidadeProdutivaModel::get() as $unidadeProdutiva) {
        //     $point = new Point($unidadeProdutiva->lat, $unidadeProdutiva->lng);

        //     if (!is_null(RegiaoModel::contains('poligono', $point)->whereIn('id', $values)->get(['id'])->first())) {
        //         $unidadesProdutivas[] = $unidadeProdutiva->id;
        //     }
        // }

        if ($alias) {
            $query->orWhereHas($alias, function ($q) use ($unidadesProdutivas) {
                $q->whereIn('unidade_produtivas.id', $unidadesProdutivas);
            });
        } else {
            $query->orWhereIn('unidade_produtivas.id', $unidadesProdutivas);
        }
    }

    /**
     * Query - Produtor
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     */
    public function queryProdutores(Builder $query, ?string $alias, ?array $values)
    {
        if (!$values || count($values) == 0) {
            return;
        }

        if ($alias) {
            $query->orWhereHas($alias, function ($q) use ($values) {
                $q->whereIn('produtores.id', $values);
            });
        } else {
            $query->orWhereIn('produtores.id', $values);
        }
    }

    /**
     * Query - Unidade Produtiva
     *
     * @param Builder $query
     * @param string $alias
     * @param array[string] array
     */
    public function queryUnidadesProdutivas(Builder $query, ?string $alias, ?array $values)
    {
        if (!$values || count($values) == 0) {
            return;
        }

        if ($alias) {
            $query->orWhereHas($alias, function ($q) use ($values) {
                $q->whereIn('unidade_produtivas.id', $values);
            });
        } else {
            $query->orWhereIn('unidade_produtivas.id', $values);
        }
    }

    /**
     * ATUAÇÃO
     */
    public function getAllDominiosAtuacao()
    {
        return DominioModel::withoutGlobalScopes()->get()->pluck('id')->toArray();
    }

    public function getAllTecnicosAtuacao()
    {
        return \Cache::store('array')->remember('getAllTecnicosAtuacao', 100,
            function() {
                return User::withoutGlobalScopes()
                    ->whereHas('roles', function ($q) {
                        $q->where('name', config('access.users.technician_role'));
                        $q->orWhere('name', config('access.users.operational_unit_role'));
                        //$q->orWhere('name', 'Dominio');   // Tive que adicionar por causa do Chart_1_13b e Chart_1_5 (Atendimento), um Domínio pode finalizar formulário e o finished_user_id fica no Domínio
                        //Não consigo vincular com a unidadesOperacionaisNS porque ele nao tem vinculo direto com Unidade Operacional
                        //Se for descomentar, tem que rever o método getTecnicosUnidOperacional
                    })
                    ->get()
                    ->pluck('id')
                    ->toArray();
            });
    }

    public function existFilterAtuacao(array $data)
    {
        return @$data['atuacao_dominio_id'] || @$data['atuacao_unidade_operacional_id'] || @$data['atuacao_tecnico_id'];
    }

    /**
     * Retorna os IDS dos técnicos que fazem parte das unidades informadas
     *
     * @param array $unidadesOperacionais
     */
    private function getTecnicosUnidOperacional($unidadesOperacionais)
    {
        return User::withoutGlobalScopes()
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Tecnico');
                $q->orWhere('name', 'Unidade Operacional');
                //$q->orWhere('name', 'Dominio'); //Não consigo vincular com a unidadesOperacionaisNS porque ele nao tem vinculo direto com Unidade Operacional
            })
            ->whereHas('unidadesOperacionaisNS', function ($q) use ($unidadesOperacionais) {
                $q->whereIn('unidade_operacionais.id', $unidadesOperacionais);
            })
            ->get()
            ->pluck('id')
            ->toArray();
    }

    /**
     * Retorna a lista de técnicos de acordo com ID Domínios, ID Unidades Operacionais e ID Técnicos
     *
     * @param array|null $dominio_id
     * @param array|null $unidade_operacional_id
     * @param array|null $tecnico_id
     *
     * @return array
     */
    public function getTecnicos(?array $dominio_id, ?array $unidade_operacional_id, ?array $tecnico_id)
    {
        //Melhora considerável utilizando o cache por array (só cacheia a primeira interação)
        return \Cache::store('array')->remember('getTecnicos_dominios_'.AppHelper::getCacheKey($dominio_id, $unidade_operacional_id, $tecnico_id), 100,
            function() use ($dominio_id, $unidade_operacional_id, $tecnico_id) {
                $tecnicos = array();

                if ($dominio_id) {
                    $unidadesOperacionais = UnidadeOperacionalModel::withoutGlobalScopes()->whereIn('dominio_id', $dominio_id)->pluck('id');
                    $tecnicos = array_merge($tecnicos, $this->getTecnicosUnidOperacional($unidadesOperacionais));
                }

                if ($unidade_operacional_id) {
                    $tecnicos = array_merge($tecnicos, $this->getTecnicosUnidOperacional($unidade_operacional_id));
                }

                if ($tecnico_id) {
                    $tecnicos = array_merge($tecnicos, $tecnico_id);
                }

                return array_unique($tecnicos);
            });
    }

    /**
     * Retorna a data formatada para consulta no BD
     *
     * @param string $dt_ini
     * @param string $dt_end
     *
     * @return array
     */
    private function dateBetween($dt_ini, $dt_end)
    {
        return [$dt_ini . " 00:00:00", $dt_end . " 23:59:59"];
    }

    /**
     * Extrai o período Inicial e Final
     * @param mixed $period = '00/0000'
     * @param mixed $dt_ini = '2020-12-01'
     * @param mixed $dt_end = '2020-12-31'
     *
     * @return array
     */
    public function period($period, $dt_ini = null, $dt_end = null)
    {
        $dtIniMonth = DateTime::createFromFormat('d/m/Y', '01/' . $period);
        if ($dt_ini) {
            $dt_ini = DateTime::createFromFormat('Y-m-d', $dt_ini);

            //Se for o mesmo mês, o inicio começa pelo que o usuário informou no filtro
            if ($dtIniMonth->format('Y-m') === $dt_ini->format('Y-m')) {
                $dtIniMonth = $dt_ini;
            }
        }

        $dtEndMonth = DateTime::createFromFormat('d/m/Y', '01/' . $period)->modify('last day of');
        if ($dt_end) {
            $dt_end = DateTime::createFromFormat('Y-m-d', $dt_end);

            //Se for o mesmo mês, o final começa pelo que o usuário informou no filtro
            if ($dtEndMonth->format('Y-m') === $dt_end->format('Y-m')) {
                $dtEndMonth = $dt_end;
            }
        }

        return [$dtIniMonth->format('Y-m-d') . " 00:00:00", $dtEndMonth->format('Y-m-d') . " 23:59:59"];
    }

    /**
     * Query Atuação - Produtor
     *
     * @param Builder $query
     * @param string|null $alias
     * @param array|null $dominio_id
     * @param array|null $unidade_operacional_id
     * @param array|null $tecnico_id
     * @param string|null $dt_ini
     * @param string|null $dt_end
     */
    public function queryAtuacaoProdutor(Builder $query, ?string $alias, ?array $dominio_id, ?array $unidade_operacional_id, ?array $tecnico_id, ?string $dt_ini = '', ?string $dt_end = '')
    {
        $tecnicos_id = $this->getTecnicos($dominio_id, $unidade_operacional_id, $tecnico_id);

        if (count($tecnicos_id) == 0 || !$dt_ini || !$dt_end) {
            return;
        }

        if (@$alias) {
            //Produtor
            $query->orWhereHas($alias, function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                if ($dt_ini && $dt_end) {
                    $q->whereBetween('produtores.created_at', $this->dateBetween($dt_ini, $dt_end));
                }

                if ($tecnicos_id && count($tecnicos_id) > 0) {
                    $q->whereIn('produtores.user_id', $tecnicos_id);
                }
            });
        } else {
            $query->orWhere(function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                if ($dt_ini && $dt_end) {
                    $q->whereBetween('produtores.created_at', $this->dateBetween($dt_ini, $dt_end));
                }

                if ($tecnicos_id && count($tecnicos_id) > 0) {
                    $q->whereIn('produtores.user_id', $tecnicos_id);
                }
            });
        }
    }


    /**
     * Query Atuação - Unidade Produtiva
     *
     * @param Builder $query
     * @param string|null $alias
     * @param array|null $dominio_id
     * @param array|null $unidade_operacional_id
     * @param array|null $tecnico_id
     * @param string|null $dt_ini
     * @param string|null $dt_end
     */
    public function queryAtuacaoUnidadeProdutiva(Builder $query, ?string $alias, ?array $dominio_id, ?array $unidade_operacional_id, ?array $tecnico_id, ?string $dt_ini = '', ?string $dt_end = '')
    {
        $tecnicos_id = $this->getTecnicos($dominio_id, $unidade_operacional_id, $tecnico_id);

        if (count($tecnicos_id) == 0 || !$dt_ini || !$dt_end) {
            return;
        }

        //Unidade Produtiva
        if ($alias) {
            $query->orWhereHas($alias, function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                if ($dt_ini && $dt_end) {
                    $q->whereBetween('unidade_produtivas.created_at', $this->dateBetween($dt_ini, $dt_end));
                }

                if ($tecnicos_id && count($tecnicos_id) > 0) {
                    $q->whereIn('unidade_produtivas.user_id', $tecnicos_id);
                }
            });
        } else {
            $query->orWhere(function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                if ($dt_ini && $dt_end) {
                    $q->whereBetween('unidade_produtivas.created_at', $this->dateBetween($dt_ini, $dt_end));
                }

                if ($tecnicos_id && count($tecnicos_id) > 0) {
                    $q->whereIn('unidade_produtivas.user_id', $tecnicos_id);
                }
            });
        }
    }

    /**
     * Query Atuação - Caderno de campo
     *
     * @param Builder $query
     * @param string|null $alias
     * @param array|null $dominio_id
     * @param array|null $unidade_operacional_id
     * @param array|null $tecnico_id
     * @param string|null $dt_ini
     * @param string|null $dt_end
     */
    public function queryAtuacaoCadernoDeCampo(Builder $query, ?string $alias, ?array $dominio_id, ?array $unidade_operacional_id, ?array $tecnico_id, ?string $dt_ini = '', ?string $dt_end = '')
    {
        $tecnicos_id = $this->getTecnicos($dominio_id, $unidade_operacional_id, $tecnico_id);

        if (count($tecnicos_id) == 0 || !$dt_ini || !$dt_end) {
            return;
        }

        if ($alias) {
            $query->orWhereHas($alias, function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                $q->whereRaw('cadernos.unidade_produtiva_id = unidade_produtivas.id'); //Fix p/ resolver os 3 níveis de busca (unidade_produtiva.produtores.cadernos) //Só retorna cadernos dos produtores x unidades produtivas

                if ($dt_ini && $dt_end) {
                    $q->where(function($qq) use ($dt_ini, $dt_end) {
                        $qq->whereBetween('cadernos.created_at', $this->dateBetween($dt_ini, $dt_end));
                        $qq->orWhereBetween('cadernos.finished_at', $this->dateBetween($dt_ini, $dt_end)); //Como considera o finish_user_id, a data de finalização tb entra
                    });
                }

                if ($tecnicos_id) {
                    $q->where(function ($qq) use ($tecnicos_id) {
                        $qq->whereIn('cadernos.user_id', $tecnicos_id);
                        $qq->orWhereIn('cadernos.finish_user_id', $tecnicos_id);
                    });
                }

                $q->where('cadernos.status', CadernoStatusEnum::Finalizado);
            });
        } else {
            $query->orWhere(function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                if ($dt_ini && $dt_end) {
                    $q->where(function($qq) use ($dt_ini, $dt_end) {
                        $qq->whereBetween('cadernos.created_at', $this->dateBetween($dt_ini, $dt_end));
                        $qq->orWhereBetween('cadernos.finished_at', $this->dateBetween($dt_ini, $dt_end));
                    });
                }

                if ($tecnicos_id) {
                    $q->where(function ($qq) use ($tecnicos_id) {
                        $qq->whereIn('cadernos.user_id', $tecnicos_id);
                        $qq->orWhereIn('cadernos.finish_user_id', $tecnicos_id);
                    });
                }

                $q->where('cadernos.status', CadernoStatusEnum::Finalizado);
            });
        }
    }

    /**
     * Query Atuação - Formulário
     *
     * @param Builder $query
     * @param string|null $alias
     * @param array|null $dominio_id
     * @param array|null $unidade_operacional_id
     * @param array|null $tecnico_id
     * @param string|null $dt_ini
     * @param string|null $dt_end
     */
    public function queryAtuacaoFormulario(Builder $query, ?string $alias, ?array $dominio_id, ?array $unidade_operacional_id, ?array $tecnico_id, ?string $dt_ini = '', ?string $dt_end = '')
    {
        $tecnicos_id = $this->getTecnicos($dominio_id, $unidade_operacional_id, $tecnico_id);

        if (count($tecnicos_id) == 0 || !$dt_ini || !$dt_end) {
            return;
        }

        if ($alias) {
            $query->orWhereHas($alias, function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                if ($dt_ini && $dt_end) {
                    $q->where(function ($qq) use ($dt_ini, $dt_end) {
                        $qq->whereBetween('checklist_unidade_produtivas.created_at', $this->dateBetween($dt_ini, $dt_end));
                        $qq->orWhereBetween('checklist_unidade_produtivas.updated_at', $this->dateBetween($dt_ini, $dt_end));
                        $qq->orWhereBetween('checklist_unidade_produtivas.finished_at', $this->dateBetween($dt_ini, $dt_end));
                    });
                }

                if ($tecnicos_id) {
                    $q->where(function ($qq) use ($tecnicos_id) {
                        $qq->whereIn('checklist_unidade_produtivas.user_id', $tecnicos_id);
                        $qq->orWhereIn('checklist_unidade_produtivas.finish_user_id', $tecnicos_id);
                    });
                }

                $q->whereNotIn('checklist_unidade_produtivas.status', [ChecklistStatusEnum::Rascunho]);
            });
        } else {
            $query->orWhere(function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                if ($dt_ini && $dt_end) {
                    $q->where(function ($qq) use ($dt_ini, $dt_end) {
                        $qq->whereBetween('checklist_unidade_produtivas.created_at', $this->dateBetween($dt_ini, $dt_end));
                        $qq->orWhereBetween('checklist_unidade_produtivas.updated_at', $this->dateBetween($dt_ini, $dt_end));
                        $qq->orWhereBetween('checklist_unidade_produtivas.finished_at', $this->dateBetween($dt_ini, $dt_end));
                    });
                }

                if ($tecnicos_id) {
                    $q->where(function ($qq) use ($tecnicos_id) {
                        $qq->whereIn('checklist_unidade_produtivas.user_id', $tecnicos_id);
                        $qq->orWhereIn('checklist_unidade_produtivas.finish_user_id', $tecnicos_id);
                    });
                }

                $q->whereNotIn('checklist_unidade_produtivas.status', [ChecklistStatusEnum::Rascunho]);
            });
        }
    }

    /**
     * Query Atuação - Plano de ação / Plano de ação - Item / Plano de ação - Histórico / Plano de ação - Item - Histórico
     *
     * $alias
     *  - Sem alias, vai pegar todos PDAS, inclusive os que não possuem unidade produtiva (PAI do Coletivo)
     *  - Com alias, vai ignorar os PDAS PAIS (Coletivo)
     *
     * @param Builder $query
     * @param string|null $alias
     * @param array|null $dominio_id
     * @param array|null $unidade_operacional_id
     * @param array|null $tecnico_id
     * @param string|null $dt_ini
     * @param string|null $dt_end
     *
     * @return [type]
     */
    public function queryAtuacaoPlanoAcao(Builder $query, ?string $alias, ?array $dominio_id, ?array $unidade_operacional_id, ?array $tecnico_id, ?string $dt_ini = '', ?string $dt_end = '')
    {
        $tecnicos_id = $this->getTecnicos($dominio_id, $unidade_operacional_id, $tecnico_id);

        if (count($tecnicos_id) == 0 || !$dt_ini || !$dt_end) {
            return;
        }

        // Plano de ação
        if ($alias) {
            $query->orWhereHas($alias, function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                if ($dt_ini && $dt_end) {
                    $q->where(function ($qq) use ($dt_ini, $dt_end) {
                        $qq->whereBetween('plano_acoes.created_at', $this->dateBetween($dt_ini, $dt_end));
                        $qq->orWhereBetween('plano_acoes.updated_at', $this->dateBetween($dt_ini, $dt_end));
                    });
                }

                if ($tecnicos_id) {
                    $q->whereIn('plano_acoes.user_id', $tecnicos_id);
                }

                // $q->whereNotIn('plano_acoes.status', [PlanoAcaoStatusEnum::Rascunho]);
            });
        } else {
            $query->orWhere(function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                if ($dt_ini && $dt_end) {
                    $q->where(function ($qq) use ($dt_ini, $dt_end) {
                        $qq->whereBetween('plano_acoes.created_at', $this->dateBetween($dt_ini, $dt_end));
                        $qq->orWhereBetween('plano_acoes.updated_at', $this->dateBetween($dt_ini, $dt_end));
                    });
                }

                if ($tecnicos_id) {
                    $q->whereIn('plano_acoes.user_id', $tecnicos_id);
                }

                // $q->whereNotIn('plano_acoes.status', [PlanoAcaoStatusEnum::Rascunho]);
            });
        }

        //Acompanhamento/histórico
        if ($alias) {
            $query->orWhereHas($alias, function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                // $q->whereNotIn('plano_acoes.status', [PlanoAcaoStatusEnum::Rascunho]);

                $q->whereHas('historicos', function ($qHist) use ($dt_ini, $dt_end, $tecnicos_id) {
                    if ($dt_ini && $dt_end) {
                        $qHist->where(function ($qq) use ($dt_ini, $dt_end) {
                            $qq->whereBetween('plano_acao_historicos.created_at', $this->dateBetween($dt_ini, $dt_end));
                            $qq->orWhereBetween('plano_acao_historicos.updated_at', $this->dateBetween($dt_ini, $dt_end));
                        });
                    }

                    if ($tecnicos_id) {
                        $qHist->whereIn('plano_acao_historicos.user_id', $tecnicos_id);
                    }
                });
            });
        } else {
            $query->orWhere(function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                // $q->whereNotIn('plano_acoes.status', [PlanoAcaoStatusEnum::Rascunho]);

                $q->whereHas('historicos', function ($qHist) use ($dt_ini, $dt_end, $tecnicos_id) {
                    if ($dt_ini && $dt_end) {
                        $qHist->where(function ($qq) use ($dt_ini, $dt_end) {
                            $qq->whereBetween('plano_acao_historicos.created_at', $this->dateBetween($dt_ini, $dt_end));
                            $qq->orWhereBetween('plano_acao_historicos.updated_at', $this->dateBetween($dt_ini, $dt_end));
                        });
                    }

                    if ($tecnicos_id) {
                        $qHist->whereIn('plano_acao_historicos.user_id', $tecnicos_id);
                    }
                });
            });
        }

        //Plano de ação - item
        if ($alias) {
            $query->orWhereHas($alias, function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                // $q->whereNotIn('plano_acoes.status', [PlanoAcaoStatusEnum::Rascunho]);

                if ($tecnicos_id) {
                    $q->whereIn('plano_acoes.user_id', $tecnicos_id);
                }

                $q->whereHas('itens', function ($qHist) use ($dt_ini, $dt_end) {
                    if ($dt_ini && $dt_end) {
                        $qHist->where(function ($qq) use ($dt_ini, $dt_end) {
                            $qq->whereBetween('plano_acao_itens.created_at', $this->dateBetween($dt_ini, $dt_end));
                            $qq->orWhereBetween('plano_acao_itens.updated_at', $this->dateBetween($dt_ini, $dt_end));
                        });
                    }
                });
            });
        } else {
            $query->orWhere(function ($q) use ($dt_ini, $dt_end, $tecnicos_id) {
                // $q->whereNotIn('plano_acoes.status', [PlanoAcaoStatusEnum::Rascunho]);

                if ($tecnicos_id) {
                    $q->whereIn('plano_acoes.user_id', $tecnicos_id);
                }

                $q->whereHas('itens', function ($qHist) use ($dt_ini, $dt_end) {
                    if ($dt_ini && $dt_end) {
                        $qHist->where(function ($qq) use ($dt_ini, $dt_end) {
                            $qq->whereBetween('plano_acao_itens.created_at', $this->dateBetween($dt_ini, $dt_end));
                            $qq->orWhereBetween('plano_acao_itens.updated_at', $this->dateBetween($dt_ini, $dt_end));
                        });
                    }
                });
            });
        }

        //Acompanhamento/Histórico - Item
        if ($alias) {
            $query->orWhereHas($alias, function ($q) use ($dt_ini, $dt_end, $tecnicos_id, $alias) {
                // $q->whereNotIn('plano_acoes.status', [PlanoAcaoStatusEnum::Rascunho]);

                if ($tecnicos_id) {
                    $q->whereIn('plano_acoes.user_id', $tecnicos_id);
                }

                $q->whereHas('itens.historicos', function ($qHist) use ($dt_ini, $dt_end) {
                    if ($dt_ini && $dt_end) {
                        $qHist->where(function ($qq) use ($dt_ini, $dt_end) {
                            $qq->whereBetween('plano_acao_item_historicos.created_at', $this->dateBetween($dt_ini, $dt_end));
                            $qq->orWhereBetween('plano_acao_item_historicos.updated_at', $this->dateBetween($dt_ini, $dt_end));
                        });
                    }
                });
            });
        } else {
            $query->orWhere(function ($q) use ($dt_ini, $dt_end, $tecnicos_id, $alias) {
                // $q->whereNotIn('plano_acoes.status', [PlanoAcaoStatusEnum::Rascunho]);

                if ($tecnicos_id) {
                    $q->whereIn('plano_acoes.user_id', $tecnicos_id);
                }

                $q->whereHas('itens.historicos', function ($qHist) use ($dt_ini, $dt_end) {
                    if ($dt_ini && $dt_end) {
                        $qHist->where(function ($qq) use ($dt_ini, $dt_end) {
                            $qq->whereBetween('plano_acao_item_historicos.created_at', $this->dateBetween($dt_ini, $dt_end));
                            $qq->orWhereBetween('plano_acao_item_historicos.updated_at', $this->dateBetween($dt_ini, $dt_end));
                        });
                    }
                });
            });
        }
    }
}
