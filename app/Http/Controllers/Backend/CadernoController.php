<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\General\AppHelper;
use App\Http\Controllers\Backend\Forms\CadernoForm;
use App\Http\Controllers\Controller;
use App\Models\Core\CadernoModel;
use App\Models\Core\CadernoRespostaCadernoModel;
use App\Models\Core\TemplateModel;
use App\Repositories\Backend\Core\CadernoRepository;
use DataTables;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Enums\CadernoStatusEnum;
use App\Enums\TipoTemplatePerguntaEnum;
use App\Http\Controllers\Backend\Traits\CadernoArquivosTrait;
use App\Models\Core\ProdutorModel;
use App\Models\Core\UnidadeProdutivaModel;
use App\Repositories\Backend\Core\CadernoArquivoRepository;
use App\Services\CadernoNotificationService;
use Carbon\Carbon;

class CadernoController extends Controller
{
    use FormBuilderTrait;
    use CadernoArquivosTrait;

    protected $repository;
    protected $repositoryArquivo;

    public function __construct(CadernoRepository $repository, CadernoArquivoRepository $repositoryArquivo)
    {
        $this->repository = $repository;
        $this->repositoryArquivo = $repositoryArquivo;
    }

    /**
     * Listagem principal do Caderno de Campo de acordo com o CadernoPermissionScope
     *
     * @param  mixed $produtor
     * @return void
     */
    public function index(ProdutorModel $produtor)
    {
        $datatableUrl = route('admin.core.cadernos.datatable', ['produtor' => @$produtor]);

        $showLinkExcluidos = true;

        return view('backend.core.cadernos.index', compact('datatableUrl', 'showLinkExcluidos'));
    }

    /**
     * API Datatable "index()"
     *
     * Listagem principal do Caderno de Campo - Retorno dos dados p/ consumo - DataTable
     *
     * Ao passar o produtor, é filtrado os cadernos somente daquele produtor
     *
     * @param  ProdutorModel $produtor
     * @return mixed
     */
    public function datatable(ProdutorModel $produtor)
    {
        $model = CadernoModel::with(['template:id,nome', 'produtor:id,nome', 'datatable_unidade_produtiva:id,nome', 'usuario:id,first_name,last_name', 'tecnicas:first_name'])->select("cadernos.*");

        $data = $produtor->id ? $model->where('produtor_id', $produtor->id) : $model;

        return DataTables::of($data)
            ->editColumn('usuario.first_name', function ($row) {
                return @$row->usuario->full_name;
            })
            ->editColumn('status', function ($row) {
                $classBadge = @$row->status == CadernoStatusEnum::Rascunho ? 'text-danger' : 'text-primary';

                return '<span class="' . $classBadge . ' font-weight-normal">' . CadernoStatusEnum::toSelectArray()[$row->status] . '</span>';
            })->addColumn('actions', function ($row) {
                $editUrl = route('admin.core.cadernos.edit', $row->id);
                $viewUrl = route('admin.core.cadernos.view', $row->id);
                $deleteUrl = route('admin.core.cadernos.destroy', $row->id);

                $downloadUrl = route('admin.core.cadernos.pdf', $row->id);
                $sendEmailUrl = route('admin.core.cadernos.sendEmail', $row->id);

                return view('backend.core.cadernos.form_actions', compact('editUrl', 'deleteUrl', 'viewUrl', 'downloadUrl', 'sendEmailUrl', 'row'));
            })->addColumn('updated_at_formatted', function ($row) {
                return $row->updated_at_formatted;
            })->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    /**
     * Visualização do Caderno de Campo
     *
     * @param  CadernoModel $caderno
     * @return void
     */
    public function view(CadernoModel $caderno)
    {
        $title = 'Caderno de Campo';

        $perguntas = $caderno->getPerguntasRespostas();
        $arquivos = $caderno->arquivos;

        $back = AppHelper::prevUrl(route('admin.core.cadernos.index'));
        return view('backend.core.cadernos.view', compact('caderno', 'title', 'perguntas', 'arquivos', 'back'));
    }

    /**
     * Retorna o template habilitado para o usuário utilizar. (Via TemplatePermissionScope, retorna o template do domínio do usuário)
     *
     * @return TemplateModel
     */
    private function getTemplate()
    {
        $template = TemplateModel::where('tipo', 'caderno')->whereNull('deleted_at')->first();

        return $template;
    }

    /**
     * Redireciona o usuário não encontre template cadastrado no domínio
     *
     * @return void
     */
    private function redirectTemplateNotfound()
    {
        return redirect(route('admin.dashboard'))->withErrors('É preciso ter um Modelo do Caderno de Campo p/ prosseguir.');
    }

    /**
     * Cadastro
     *
     * @param  FormBuilder $formBuilder
     * @param  ProdutorModel $produtor
     * @param  UnidadeProdutivaModel $unidadeProdutiva
     * @return void
     */
    public function create(FormBuilder $formBuilder, ProdutorModel $produtor, UnidadeProdutivaModel $unidadeProdutiva)
    {
        $template = $this->getTemplate();
        if (!$template) {
            return $this->redirectTemplateNotfound();
        }

        $checkExistCaderno = \App\Models\Core\CadernoModel::where('produtor_id', $produtor->id)->where('unidade_produtiva_id', $unidadeProdutiva->id)->where('status', \App\Enums\CadernoStatusEnum::Rascunho)->where("template_id", $template->id)->first();
        if ($checkExistCaderno) {
            return redirect(route('admin.core.cadernos.edit', $checkExistCaderno->id));
        }

        $form = $formBuilder->create(CadernoForm::class, [
            'id' => 'form-builder',
            'method' => 'POST',
            'url' => route('admin.core.cadernos.store'),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => ['template_id' => $template->id, 'produtor_id' => $produtor->id, 'unidade_produtiva_id' => $unidadeProdutiva->id],
            'data' => ['template' => $template, 'produtor' => $produtor, 'unidadeProdutiva' => $unidadeProdutiva]
        ]);

        $title = 'Aplicar Caderno de Campo';

        $back = AppHelper::prevUrl(route('admin.core.cadernos.index'));
        return view('backend.core.cadernos.create_update', compact('form', 'title', 'back'));
    }

    /**
     * Salva as respostas do caderno de campo
     *
     * Refatorar futuramente p/ ficar dentro do repo.
     *
     * @param  Request $request
     * @param  CadernoModel $cadernoModel
     * @return void
     */
    private function saveRespostas(Request $request, CadernoModel $cadernoModel)
    {
        //Retornas todas as perguntas respondidas no formulário (vem empilhado um array de "números" com as respostas)
        $questions = array_filter($request->all(), function ($k) {
            return \is_numeric($k);
        }, ARRAY_FILTER_USE_KEY);

        //Extrai os tipos das perguntas, id => tipo
        $perguntas = TemplateModel::where('id', $request['template_id'])->first()->perguntas()->get();
        $perguntasTipo = array();
        foreach ($perguntas as $k => $v) {
            $perguntasTipo[$v['id']] = $v['tipo'];
        }

        foreach ($questions as $k => $v) {
            //Extrai o tipo de pergunta, para saber se será salvo no "resposta_id" ou no "resposta" (caso texto ou data)
            $isTexto = $perguntasTipo[$k] == (TipoTemplatePerguntaEnum::Text || TipoTemplatePerguntaEnum::Data);

            $template_resposta_id = $isTexto ? null : $v;
            $resposta = $isTexto ? $v : null;

            if ($v) {
                $where = ['caderno_id' => $cadernoModel->id, 'template_pergunta_id' => $k];

                //Se for multiplas respostas, remove todas e depois restaura as que foram selecionadas (o funcionamento é assim por causa do Sync com o APP)
                if (is_array($v)) {
                    CadernoRespostaCadernoModel::where($where)->withTrashed()->update(['deleted_at' => Carbon::now()]);
                    foreach ($v as $kk => $vv) {
                        CadernoRespostaCadernoModel::withTrashed()->updateOrCreate(array_merge($where, ['template_resposta_id' => $vv]), ['resposta' => null])->restore();
                    }
                } else {
                    CadernoRespostaCadernoModel::updateOrCreate(['caderno_id' => $cadernoModel->id, 'template_pergunta_id' => $k], ['template_resposta_id' => $template_resposta_id, 'resposta' => @$resposta]);
                }
            }
        }
    }

    /**
     * Cadastro do formulário - POST
     *
     * @param  Request $request
     * @return void
     */
    public function store(Request $request)
    {

        $data = $request->only(['template_id', 'produtor_id', 'unidade_produtiva_id', 'status', 'custom-redirect']);
        $tecnicas = $request->only(['tecnicas']);
        $produtor = \App\Models\Core\ProdutorModel::where('id', $data['produtor_id'])->first();
        $unidadeProdutiva = \App\Models\Core\UnidadeProdutivaModel::where('id', $data['unidade_produtiva_id'])->first();

        $messageSuccess = 'Caderno criado com sucesso!';

        $form = $this->form(CadernoForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            // 'url' => route('admin.core.cadernos.update', compact('caderno')),
            'class' => 'needs-validation',
            'novalidate' => true,
            // 'model' => $caderno,
            'data' => ['produtor' => $produtor, 'unidadeProdutiva' => $unidadeProdutiva],
        ]);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['template_id', 'produtor_id', 'unidade_produtiva_id', 'status', 'custom-redirect']);
        if (@$data['custom-redirect']) {
            $data['status'] = CadernoStatusEnum::Rascunho;
            $messageSuccess = $messageSuccess . ' O status do Caderno de Campo foi alterado para rascunho p/ permitir a inclusão de novos arquivos. Lembre-se de alterar o status p/ finalizado após adicionar os novos arquivos.';
        }

        $cadernoModel = $this->repository->create($data);
        $this->saveRespostas($request, $cadernoModel);

        $tecnicas = $tecnicas['tecnicas'];
        $cadernoModel->tecnicas()->sync($tecnicas);

        /*Custom Redirect*/
        $redirect = route('admin.core.produtor.dashboard', ['produtor' => $data['produtor_id']]);
        if (@$data['custom-redirect']) {
            $redirect = route('admin.core.cadernos.edit', [$cadernoModel->id, '#' . $data['custom-redirect']]);
        }
        /*End Custom Redirect*/

        return redirect($redirect)->withFlashSuccess($messageSuccess);
    }

    /**
     * Retorno das respostas para a Edição
     *
     * id da pergunta => resposta
     *
     * @param  CadernoModel $caderno
     * @return array
     */
    private function getRespostas(CadernoModel $caderno)
    {
        $respostasCaderno = $caderno->respostasMany()->get()->toArray();

        $respostas = array();

        foreach ($respostasCaderno as $k => $v) {
            $value = @$v['template_resposta_id'] ? $v['template_resposta_id'] : $v['resposta'];

            //Tratamento especifico para o array (questões de multipla escolha)
            if (@$respostas[$v['template_pergunta_id']]) {
                $oldValue = $respostas[$v['template_pergunta_id']];
                if (!is_array($oldValue)) {
                    $oldValue = [$oldValue];
                }
                $value = array_merge($oldValue, [$value]);
            }

            $respostas[$v['template_pergunta_id']] = $value;
        }

        return $respostas;
    }

    /**
     * Edição do formulário
     *
     * @param  mixed $caderno
     * @param  mixed $formBuilder
     * @return void
     */
    public function edit(CadernoModel $caderno, FormBuilder $formBuilder)
    {
        //Não permite a edição de um formulário já finalizado (através do Policy o botão de editar nem aparece mais)
        if ($caderno->status === CadernoStatusEnum::Finalizado) {
            return redirect()->route('admin.core.cadernos.index')->withFlashDanger('Não é possível editar um Caderno de Campo finalizado!');
        }

        //Retorna as respostas do caderno selecionado
        $respostas = $this->getRespostas($caderno);
        foreach ($respostas as $k => $v) {
            $caderno[$k] = $v;
        }

        //Valida se no momento da edição existe um template válido para o usuário
        $template = $this->getTemplate();
        if (!$template) {
            return $this->redirectTemplateNotfound();
        }

        $form = $formBuilder->create(CadernoForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            'url' => route('admin.core.cadernos.update', compact('caderno')),
            'class' => 'needs-validation',
            'novalidate' => true,
            'model' => $caderno,
            'data' => ['template' => $template, 'produtor' => $caderno->produtor, 'unidadeProdutiva' => $caderno->unidadeProdutiva]
        ]);

        $title = 'Editar caderno de campo';

        //Iframe dos arquivos vinculados ao caderno (ver CadernoArquivosTrait.php)
        $arquivosId = 'iframeArquivos';
        $arquivosSrc = route('admin.core.cadernos.arquivos.index', compact('caderno'));

        $back = AppHelper::prevUrl(route('admin.core.cadernos.index'));
        return view('backend.core.cadernos.create_update', compact('form', 'title', 'caderno', 'arquivosId', 'arquivosSrc', 'back'));
    }

    /**
     * Edição do formulário - POST
     *
     * @param  Request $request
     * @param  CadernoModel $caderno
     * @return void
     */
    public function update(Request $request, CadernoModel $caderno)
    {

        $data = $request->only(['template_id', 'produtor_id', 'unidade_produtiva_id', 'status', 'custom-redirect']);
        $tecnicas = $request->only(['tecnicas']);

        $produtor = \App\Models\Core\ProdutorModel::where('id', $data['produtor_id'])->first();
        $unidadeProdutiva = \App\Models\Core\UnidadeProdutivaModel::where('id', $data['unidade_produtiva_id'])->first();

        $form = $this->form(CadernoForm::class, [
            'id' => 'form-builder',
            'method' => 'PATCH',
            // 'url' => route('admin.core.cadernos.update', compact('caderno')),
            'class' => 'needs-validation',
            'novalidate' => true,
            // 'model' => $caderno,
            'data' => ['produtor' => $produtor, 'unidadeProdutiva' => $unidadeProdutiva],
        ]);

        //Válida se todos os campos obrigatórios foram preenchidos
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $data = $request->only(['template_id', 'produtor_id', 'unidade_produtiva_id', 'status']);
        $cadernoModel = $this->repository->update($caderno, $data);

        $tecnicas = $tecnicas['tecnicas'];
        $cadernoModel->tecnicas()->sync($tecnicas);

        //Faz um touch no caderno, para atualizar a data de atualização (mesmo que não tenha nenhuma informação alterada)
        $cadernoModel->touch();

        //Salva as respostas
        $this->saveRespostas($request, $cadernoModel);

        return redirect()->route('admin.core.cadernos.index')->withFlashSuccess('Caderno alterado com sucesso!');
    }


    /**
     * Remover caderno de campo
     *
     * @param  mixed $caderno
     * @return void
     *
     */
    public function destroy(CadernoModel $caderno)
    {
        $this->repository->delete($caderno);

        return redirect()->route('admin.core.cadernos.index')->withFlashSuccess('Caderno removido com sucesso!');
    }


    /**
     * Retorno do produtor/unidade produtiva
     *
     * Caso seja passado o "produtor", é feito uma filtragem
     *
     * @param  ProdutorModel $produtor
     * @return Builder
     */
    private function getProdutorUnidadeProdutiva(ProdutorModel $produtor)
    {
        $sql = UnidadeProdutivaModel::with(['cidade:id,nome', 'estado:id,nome'])
            ->select('produtores.uid', 'produtores.nome', 'produtores.cpf', 'produtores.cnpj', 'produtores.id as produtor_id', 'unidade_produtivas.uid as unidade_produtiva_uid', 'unidade_produtivas.id as unidade_produtiva_id', 'unidade_produtivas.nome as unidade_produtiva', 'unidade_produtivas.cidade_id', 'unidade_produtivas.estado_id', 'unidade_produtivas.socios')
            ->join('produtor_unidade_produtiva', 'unidade_produtivas.id', '=', 'produtor_unidade_produtiva.unidade_produtiva_id')
            ->join('produtores', 'produtores.id', '=', 'produtor_unidade_produtiva.produtor_id')
            ->whereNull('produtor_unidade_produtiva.deleted_at');

        if (@$produtor->id) {
            $sql->where('produtores.id', $produtor->id);
        }

        return $sql;
    }

    /**
     * Listagem de produtores/unidades produtivas p/ seleção no momento de "aplicar" um caderno de campo
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function produtorUnidadeProdutiva(ProdutorModel $produtor)
    {
        //Se tiver apenas um produtor no retorno da listagem, redireciona para a tela de criação do caderno de campo com o produtor selecionado
        $data = $this->getProdutorUnidadeProdutiva($produtor);
        if ($data->count() == 1) {
            $row = $data->first();
            return redirect(route('admin.core.cadernos.create', ['produtor' => $row->produtor_id, 'unidadeProdutiva' => $row->unidade_produtiva_id]));
        }

        $datatableUrl = route('admin.core.cadernos.datatable_produtor_unidade_produtiva', ['produtor' => @$produtor]);
        return view('backend.core.cadernos.produtor_unidade_produtiva', compact('datatableUrl'));
    }

    /**
     * API Datatable "produtorUnidadeProdutiva()"
     *
     * @param  ProdutorModel $produtor
     * @return void
     */
    public function datatableProdutorUnidadeProdutiva(ProdutorModel $produtor)
    {
        $data = $this->getProdutorUnidadeProdutiva($produtor);

        return DataTables::of($data)
            ->editColumn('uid', function ($row) {
                return $row->uid . ' - ' . $row->unidade_produtiva_uid;
            })->editColumn('cpf', function ($row) {
                return AppHelper::formatCpfCnpj($row->cpf ? $row->cpf : $row->cnpj);
            })->addColumn('actions', function ($row) {
                $addUrl = route('admin.core.cadernos.create', ['produtor' => $row->produtor_id, 'unidadeProdutiva' => $row->unidade_produtiva_id]);
                return view('backend.components.form-actions.index', ['addUrl' => $addUrl, 'row' => $row]);
            })->filterColumn('nome', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('produtores.nome', 'like', '%' . $keyword . '%');
                }
            })->filterColumn('cpf', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('produtores.cpf', 'like', '%' . $keyword . '%');
                    $query->orWhere('produtores.cnpj', 'like', '%' . $keyword . '%');
                }
            })->filterColumn('unidade_produtiva', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('unidade_produtivas.nome', 'like', '%' . $keyword . '%');
                }
            })->filterColumn('cidade.nome', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('cidade', function ($q) use ($keyword) {
                        $q->where('cidades.nome', 'like', '%' . $keyword . '%');
                    });
                }
            })->filterColumn('estado.nome', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('estado', function ($q) use ($keyword) {
                        $q->where('estados.nome', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->make(true);
    }


    /**
     * Listagem de cadernos que foram removidos
     *
     * @return void
     */
    public function indexExcluidos()
    {
        $datatableUrl = route('admin.core.cadernos.datatableExcluidos');

        $title = 'Cadernos Excluídos';

        $showLinkExcluidos = false;

        return view('backend.core.cadernos.index', compact('datatableUrl', 'title', 'showLinkExcluidos'));
    }

    /**
     * API Datatable "indexExcluidos()"
     *
     * @return void
     */
    public function datatableExcluidos()
    {
        $data = CadernoModel::with(['template:id,nome', 'produtor:id,nome', 'datatable_unidade_produtiva:id,nome', 'usuario:id,first_name,last_name'])
            ->select("cadernos.*")
            ->onlyTrashed();

        return DataTables::of($data)
            ->editColumn('usuario.first_name', function ($row) {
                return $row->usuario->full_name;
            })
            ->editColumn('status', function ($row) {
                $classBadge = @$row->status == CadernoStatusEnum::Rascunho ? 'text-danger' : 'text-primary';

                return '<span class="' . $classBadge . ' font-weight-normal">' . CadernoStatusEnum::toSelectArray()[$row->status] . '</span>';
            })->addColumn('actions', function ($row) {
                $restoreUrl = route('admin.core.cadernos.restore', $row->id);
                $forceDeleteUrl = route('admin.core.cadernos.forceDelete', $row->id);

                return view('backend.components.form-actions.index', compact('restoreUrl', 'forceDeleteUrl', 'row'));
            })->addColumn('updated_at_formatted', function ($row) {
                return $row->updated_at_formatted;
            })
            ->rawColumns(['status'])
            ->make(true);

        //return view('backend.core.checklist_unidade_produtiva.form_actions', compact('restoreUrl', 'messageRestore', 'forceDeleteUrl', 'row'));
    }

    /**
     * Ação p/ restaurar um caderno de campo removido (ver regras no CadernoPolicy)
     *
     * @param CadernoModel $caderno
     * @return void
     */
    public function restore(CadernoModel $caderno)
    {
        //Caso tenha um em modo rascunho nao permite ... isso é tratado no policy, por isso a regra não esta aqui.
        $this->repository->restore($caderno);

        return redirect()->route('admin.core.cadernos.index')->withFlashSuccess('Caderno restaurado com sucesso!');
    }

    /**
     * Ação para remover "fisicamente" o registro
     *
     * @param CadernoModel $caderno
     * @return void
     */
    public function forceDelete(CadernoModel $caderno)
    {
        $this->repository->forceDelete($caderno);

        return redirect()->route('admin.core.cadernos.index')->withFlashSuccess('Caderno removido com sucesso!');
    }

    /**
     * PDF do Caderno de Campo
     *
     * @param  CadernoModel $caderno
     * @param  CadernoNotificationService $service
     * @return void
     */
    public function pdf(CadernoModel $caderno, CadernoNotificationService $service)
    {
        $pdf = $service->getCadernoPDF($caderno);

        // Para testar "inline" o pdf que foi gerado
        // return $pdf->inline();

        return $pdf->download($caderno->template->nome . '-' . $caderno->unidadeProdutiva->nome . '.pdf');
    }

    /**
     * Disparo de email (email cadastrado no produtor) do formulário aplicado
     *
     * @param  CadernoModel $caderno
     * @param  CadernoNotificationService $service
     * @return void
     */
    public function sendEmail(CadernoModel $caderno, CadernoNotificationService $service)
    {
        try {
            //Descomentar essa linha para ver como ficou o template do email p/ os dados passados
            // return (new \App\Mail\Backend\Caderno\SendCaderno($caderno, null))->render();

            $service->sendMail($caderno);
            return redirect()->route('admin.core.cadernos.index')->withFlashSuccess('E-mail enviado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('admin.core.cadernos.index')->withFlashDanger($e->getMessage());
        }
    }
}
