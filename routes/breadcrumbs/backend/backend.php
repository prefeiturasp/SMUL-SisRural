<?php


$empty = function ($trail) {
    // $trail->push('', route('admin.dashboard'));
};

Breadcrumbs::for('admin.dashboard', $empty);

/**
 * Novo Produtor vs Unidade Produtiva
 */
Breadcrumbs::for('admin.core.novo_produtor_unidade_produtiva.create', function ($trail) {
    $trail->push('Novo Produtor / Unidade Produtiva', route('admin.core.novo_produtor_unidade_produtiva.create'));
});
Breadcrumbs::for('admin.core.novo_produtor_unidade_produtiva.produtor_edit', function ($trail) {
    $trail->push('Novo Produtor / Unidade Produtiva', route('admin.core.novo_produtor_unidade_produtiva.create'));
    $trail->push('Produtor');
});
Breadcrumbs::for('admin.core.novo_produtor_unidade_produtiva.unidade_produtiva_edit', function ($trail) {
    $trail->push('Novo Produtor / Unidade Produtiva', route('admin.core.novo_produtor_unidade_produtiva.create'));
    $trail->push('Unidade Produtiva');
});

/**
 * Unidade Produtiva
 */
Breadcrumbs::for('admin.core.unidade_produtiva.index', function ($trail) {
    $trail->push('Unidade Produtiva', route('admin.core.unidade_produtiva.index'));
});

Breadcrumbs::for('admin.core.unidade_produtiva.view', function ($trail, $unidadeProdutiva) {
    $trail->parent('admin.core.unidade_produtiva.index');
    $trail->push($unidadeProdutiva->nome);
});

Breadcrumbs::for('admin.core.unidade_produtiva.create', function ($trail) {
    $trail->parent('admin.core.unidade_produtiva.index');
    $trail->push('Criar Unidade Produtiva');
});

Breadcrumbs::for('admin.core.unidade_produtiva.edit', function ($trail, $id) {
    $trail->parent('admin.core.unidade_produtiva.index');
    $trail->push('Editar Unidade Produtiva', route('admin.core.unidade_produtiva.edit', $id));
});

Breadcrumbs::for('admin.core.unidade_produtiva.arquivos.index', function ($trail, $unidadeProdutiva) {
    $trail->push('Unidade Produtiva', route('admin.core.unidade_produtiva.index'));
    $trail->push($unidadeProdutiva->nome);
});

Breadcrumbs::for('admin.core.unidade_produtiva.arquivos.create', function ($trail, $unidadeProdutiva) {
    $trail->push('Unidade Produtiva', route('admin.core.unidade_produtiva.index'));
    $trail->push($unidadeProdutiva->nome, route('admin.core.unidade_produtiva.arquivos.index', ['unidadeProdutiva' => $unidadeProdutiva]));
    $trail->push('Cadastrar Arquivo');
});

Breadcrumbs::for('admin.core.unidade_produtiva.arquivos.edit', function ($trail, $unidadeProdutiva) {
    $trail->push('Unidade Produtiva', route('admin.core.unidade_produtiva.index'));
    $trail->push($unidadeProdutiva->nome, route('admin.core.unidade_produtiva.arquivos.index', ['unidadeProdutiva' => $unidadeProdutiva]));
    $trail->push('Editar Arquivo');
});


Breadcrumbs::for('admin.core.unidade_produtiva.colaboradores.index', $empty);
Breadcrumbs::for('admin.core.unidade_produtiva.colaboradores.create', $empty);
Breadcrumbs::for('admin.core.unidade_produtiva.colaboradores.edit', $empty);

Breadcrumbs::for('admin.core.unidade_produtiva.produtor', function ($trail) {
    $trail->parent('admin.core.unidade_produtiva.index');
    $trail->push('Produtor');
});

Breadcrumbs::for('admin.core.unidade_produtiva.invalid', function ($trail) {
    $trail->push('Unidades Produtivas - Fora da Abrangência', route('admin.core.unidade_produtiva.index'));
});
/**
 * Dominio
 */
Breadcrumbs::for('admin.core.dominio.index', function ($trail) {
    $trail->push('Domínio', route('admin.core.dominio.index'));
});
Breadcrumbs::for('admin.core.dominio.create', function ($trail) {
    $trail->parent('admin.core.dominio.index');
    $trail->push('Criar Domínio', route('admin.core.dominio.create'));
});
Breadcrumbs::for('admin.core.dominio.edit', function ($trail, $id) {
    $trail->parent('admin.core.dominio.index');
    $trail->push('Editar Domínio', route('admin.core.dominio.edit', $id));
});

/**
 * Produtor
 */
Breadcrumbs::for('admin.core.produtor.index', function ($trail) {
    $trail->push('Produtor', route('admin.core.produtor.index'));
});
Breadcrumbs::for('admin.core.produtor.view', function ($trail, $produtor) {
    $trail->parent('admin.core.produtor.index');
    $trail->push($produtor->nome);
});
Breadcrumbs::for('admin.core.produtor.dashboard', function ($trail, $produtor) {
    $trail->parent('admin.core.produtor.index');
    $trail->push($produtor->nome);
});
Breadcrumbs::for('admin.core.produtor.create', function ($trail) {
    $trail->parent('admin.core.produtor.index');
    $trail->push('Criar Produtor', route('admin.core.produtor.create'));
});
Breadcrumbs::for('admin.core.produtor.edit', function ($trail, $id) {
    $trail->parent('admin.core.produtor.index');
    $trail->push('Editar Produtor', route('admin.core.produtor.edit', $id));
});
Breadcrumbs::for('admin.core.produtor.search-unidade-produtiva', $empty);
Breadcrumbs::for('admin.core.produtor.add-unidade-produtiva', $empty);
Breadcrumbs::for('admin.core.produtor.edit-unidade-produtiva', $empty);

Breadcrumbs::for('admin.core.produtor.index_sem_unidade', $empty);
Breadcrumbs::for('admin.core.produtor.edit_sem_unidade', $empty);

/**
 * Unidade Operacional
 */

$breadUnidadeOperacional = function ($trail) {
    $trail->push('Unidade Operacional', route('admin.core.unidade_operacional.index'));
};

Breadcrumbs::for('admin.core.unidade_operacional.index', $breadUnidadeOperacional);
Breadcrumbs::for('admin.core.unidade_operacional.create', $breadUnidadeOperacional);
Breadcrumbs::for('admin.core.unidade_operacional.edit', $breadUnidadeOperacional);

Breadcrumbs::for('admin.core.unidade_operacional.unidade_produtiva.index', function ($trail, $unidadeOperacional) {
    $trail->push('Unidades Operacionais', route('admin.core.unidade_operacional.index'));
    $trail->push($unidadeOperacional->nome);
    $trail->push('Unidades Produtivas');
});

Breadcrumbs::for('admin.core.unidade_operacional.unidade_produtiva.create', function ($trail, $unidadeOperacional) {
    $trail->push('Unidades Operacionais', route('admin.core.unidade_operacional.index'));
    $trail->push($unidadeOperacional->nome);
    $trail->push('Unidades Produtivas');
});

/**
 * Template Perguntas
 */
Breadcrumbs::for('admin.core.template_perguntas.index', function ($trail) {
    $trail->push('Perguntas', route('admin.core.template_perguntas.index'));
});
Breadcrumbs::for('admin.core.template_perguntas.create', function ($trail) {
    $trail->parent('admin.core.template_perguntas.index');
    $trail->push('Criar Pergunta', route('admin.core.template_perguntas.create'));
});
Breadcrumbs::for('admin.core.template_perguntas.edit', function ($trail, $id) {
    $trail->parent('admin.core.template_perguntas.index');
    $trail->push('Editar Pergunta', route('admin.core.template_perguntas.edit', $id));
});

Breadcrumbs::for('admin.core.template_perguntas.respostas.index', function ($trail, $templatePergunta) {
    $trail->push('Perguntas', route('admin.core.template_perguntas.index'));
    $trail->push($templatePergunta->pergunta . ' / Respostas');
});

Breadcrumbs::for('admin.core.template_perguntas.respostas.create', function ($trail, $templatePergunta) {
    $trail->push('Perguntas', route('admin.core.template_perguntas.index'));
    $trail->push($templatePergunta->pergunta . ' / Respostas', route('admin.core.template_perguntas.respostas.index', ['templatePergunta' => $templatePergunta]));
    $trail->push('Cadastrar Resposta');
});

Breadcrumbs::for('admin.core.template_perguntas.respostas.edit', function ($trail, $templatePergunta) {
    $trail->push('Perguntas', route('admin.core.template_perguntas.index'));
    $trail->push($templatePergunta->pergunta . ' / Respostas', route('admin.core.template_perguntas.respostas.index', ['templatePergunta' => $templatePergunta]));
    $trail->push('Editar Resposta');
});

/**
 * Templates - Caderno de Campo
 */
Breadcrumbs::for('admin.core.templates_caderno.index', function ($trail) {
    $trail->push('Modelo - Caderno de Campo', route('admin.core.templates_caderno.index'));
});
Breadcrumbs::for('admin.core.templates_caderno.create', function ($trail) {
    $trail->parent('admin.core.templates_caderno.index');
    $trail->push('Criar Modelo - Caderno de Campo', route('admin.core.templates_caderno.create'));
});
Breadcrumbs::for('admin.core.templates_caderno.edit', function ($trail, $id) {
    $trail->parent('admin.core.templates_caderno.index');
    $trail->push('Editar Modelo - Caderno de Campo', route('admin.core.templates_caderno.edit', $id));
});


Breadcrumbs::for('admin.core.templates_caderno.perguntas.index', function ($trail, $template) {
    $trail->push('Modelo - Caderno de Campo', route('admin.core.templates_caderno.index'));
    $trail->push($template->nome . ' / Perguntas Vinculadas');
});

Breadcrumbs::for('admin.core.templates_caderno.perguntas.create', function ($trail, $template) {
    $trail->push('Modelo - Caderno de Campo', route('admin.core.templates_caderno.index'));
    $trail->push($template->nome . ' / Perguntas Vinculadas', route('admin.core.templates_caderno.perguntas.index', ['template' => $template]));
    $trail->push('Vincular Pergunta');
});

Breadcrumbs::for('admin.core.templates_caderno.perguntas.edit', function ($trail, $template) {
    $trail->push('Modelo - Caderno de Campo', route('admin.core.templates_caderno.index'));
    $trail->push($template->nome, route('admin.core.templates_caderno.perguntas.index', ['template' => $template]));
    $trail->push('Editar Vinculo da Pergunta');
});

/**
 * Caderno de campo
 */

Breadcrumbs::for('admin.core.cadernos.index', function ($trail) {
    $trail->push(__('concepts.caderno_de_campo.label'), route('admin.core.cadernos.index'));
});
Breadcrumbs::for('admin.core.cadernos.excluidos', function ($trail) {
    $trail->push(__('concepts.caderno_de_campo.label'), route('admin.core.cadernos.index'));
});
Breadcrumbs::for('admin.core.cadernos.create', function ($trail) {
    $trail->parent('admin.core.cadernos.index');
    $trail->push(__('concepts.caderno_de_campo.add'));
});
Breadcrumbs::for('admin.core.cadernos.edit', function ($trail, $id) {
    $trail->parent('admin.core.cadernos.index');
    $trail->push('Editar Caderno de Campo', route('admin.core.cadernos.edit', $id));
});
Breadcrumbs::for('admin.core.cadernos.view', function ($trail, $id) {
    $trail->parent('admin.core.cadernos.index');
    $trail->push('Caderno de Campo', route('admin.core.cadernos.view', $id));
});
Breadcrumbs::for('admin.core.cadernos.produtor_unidade_produtiva', function ($trail) {
    $trail->parent('admin.core.cadernos.index');
    $trail->push('Produtor - Unidade Produtiva');
});
Breadcrumbs::for('admin.core.cadernos.pdf', function ($trail, $id) {
    $trail->parent('admin.core.cadernos.index');
    $trail->push('Visualizar Caderno de Campo', route('admin.core.cadernos.view', $id));
});

Breadcrumbs::for('admin.core.cadernos.arquivos.index', function ($trail, $caderno) {
    $trail->push('Caderno de Campo', route('admin.core.cadernos.index'));
    $trail->push($caderno->template->nome);
});

Breadcrumbs::for('admin.core.cadernos.arquivos.create', function ($trail, $caderno) {
    $trail->push('Caderno de Campo', route('admin.core.cadernos.index'));
    $trail->push($caderno->template->nome, route('admin.core.cadernos.arquivos.index', ['caderno' => $caderno]));
    $trail->push('Cadastrar Arquivo');
});

Breadcrumbs::for('admin.core.cadernos.arquivos.edit', function ($trail, $caderno) {
    $trail->push('Caderno de Campo', route('admin.core.cadernos.index'));
    $trail->push($caderno->template->nome, route('admin.core.cadernos.arquivos.index', ['caderno' => $caderno]));
    $trail->push('Editar Arquivo');
});

/*
 * Regiões
 */

$breadRegiao = function ($trail) {
    $trail->push('Região', route('admin.core.regiao.index'));
};

Breadcrumbs::for('admin.core.regiao.index', $breadRegiao);
Breadcrumbs::for('admin.core.regiao.create', $breadRegiao);
Breadcrumbs::for('admin.core.regiao.edit', $breadRegiao);

/*
 * Solo Categorias
 */

$breadSoloCategorias = function ($trail) {
    $trail->push('Uso do Solo - Categorias', route('admin.core.solo_categoria.index'));
};

Breadcrumbs::for('admin.core.solo_categoria.index', $breadSoloCategorias);
Breadcrumbs::for('admin.core.solo_categoria.create', $breadSoloCategorias);
Breadcrumbs::for('admin.core.solo_categoria.edit', $breadSoloCategorias);

/**
 * Termos de Uso
 */

Breadcrumbs::for('admin.core.termos_de_uso.index', function ($trail) {
    $trail->push('Termos de Uso', route('admin.core.termos_de_uso.index'));
});
Breadcrumbs::for('admin.core.termos_de_uso.create', function ($trail) {
    $trail->parent('admin.core.termos_de_uso.index');
    $trail->push('Criar Termos de Uso', route('admin.core.termos_de_uso.create'));
});
Breadcrumbs::for('admin.core.termos_de_uso.edit', function ($trail, $id) {
    $trail->parent('admin.core.termos_de_uso.index');
    $trail->push('Editar Termos de Uso', route('admin.core.termos_de_uso.edit', $id));
});

/**
 * Importador
 */
Breadcrumbs::for('admin.core.importador.create', function ($trail) {
    $trail->push('Importador', route('admin.core.importador.create'));
});
Breadcrumbs::for('admin.core.importador.editProdutor', function ($trail) {
    $trail->push('Importador', route('admin.core.importador.editProdutor'));
});
Breadcrumbs::for('admin.core.importador.createCaderno', function ($trail) {
    $trail->push('Importador', route('admin.core.importador.createCaderno'));
});
Breadcrumbs::for('admin.core.importador.createChecklistUnidadeProdutiva', function ($trail) {
    $trail->push('Importador', route('admin.core.importador.createChecklistUnidadeProdutiva'));
});
Breadcrumbs::for('admin.core.importador.createUsuarios', function ($trail) {
    $trail->push('Importador', route('admin.core.importador.createUsuarios'));
});

/**
 * Formulário - Perguntas (Template)
 */
Breadcrumbs::for('admin.core.perguntas.index', function ($trail) {
    $trail->push('Perguntas', route('admin.core.perguntas.index'));
});
Breadcrumbs::for('admin.core.perguntas.create', function ($trail) {
    $trail->parent('admin.core.perguntas.index');
    $trail->push('Criar Pergunta', route('admin.core.perguntas.create'));
});
Breadcrumbs::for('admin.core.perguntas.edit', function ($trail, $id) {
    $trail->parent('admin.core.perguntas.index');
    $trail->push('Editar Pergunta', route('admin.core.perguntas.edit', $id));
});

Breadcrumbs::for('admin.core.perguntas.respostas.index', function ($trail, $pergunta) {
    $trail->push('Perguntas', route('admin.core.perguntas.index'));
    $trail->push($pergunta->pergunta);
});

Breadcrumbs::for('admin.core.perguntas.respostas.create', function ($trail, $pergunta) {
    $trail->push('Perguntas', route('admin.core.perguntas.index'));
    $trail->push($pergunta->pergunta, route('admin.core.perguntas.respostas.index', ['pergunta' => $pergunta]));
    $trail->push('Cadastrar Resposta');
});

Breadcrumbs::for('admin.core.perguntas.respostas.edit', function ($trail, $pergunta) {
    $trail->push('Perguntas', route('admin.core.perguntas.index'));
    $trail->push($pergunta->pergunta, route('admin.core.perguntas.respostas.index', ['pergunta' => $pergunta]));
    $trail->push('Editar Resposta');
});

/**
 * Templates - Formulário
 */
Breadcrumbs::for('admin.core.checklist.index', function ($trail) {
    $trail->push('Formulários para Aplicação', route('admin.core.checklist.index'));
});

Breadcrumbs::for('admin.core.checklist.create', function ($trail) {
    $trail->parent('admin.core.checklist.index');
    $trail->push('Criar Formulário', route('admin.core.checklist.create'));
});

Breadcrumbs::for('admin.core.checklist.edit', function ($trail, $id) {
    $trail->parent('admin.core.checklist.index');
    $trail->push('Editar Formulário', route('admin.core.checklist.edit', $id));
});

Breadcrumbs::for('admin.core.checklist.biblioteca', function ($trail) {
    $trail->push('Biblioteca de Formulários', route('admin.core.checklist.biblioteca'));
});

/**
 * Checklist / Categorias / Perguntas
 */
Breadcrumbs::for('admin.core.checklist.categorias.perguntas.index', function ($trail, $checklistCategoria) {
    $trail->push('Formulários para Aplicação', route('admin.core.checklist.index'));
    $trail->push($checklistCategoria->nome . ' - Perguntas Vinculadas');
});

Breadcrumbs::for('admin.core.checklist.categorias.perguntas.create', function ($trail, $checklistCategoria) {
    $trail->push('Formulários para Aplicação', route('admin.core.checklist.index'));
    $trail->push($checklistCategoria->nome . ' - Perguntas Vinculadas', route('admin.core.checklist.categorias.perguntas.index', ['checklist' => $checklistCategoria->checklist_id, 'checklistCategoria' => $checklistCategoria]));
    $trail->push('Vincular Pergunta');
});

Breadcrumbs::for('admin.core.checklist.categorias.perguntas.edit', function ($trail, $checklistCategoria) {
    $trail->push('Formulários para Aplicação', route('admin.core.checklist.index'));
    $trail->push($checklistCategoria->nome . ' - Perguntas Vinculadas', route('admin.core.checklist.categorias.perguntas.index', ['checklist' => $checklistCategoria->checklist_id, 'checklistCategoria' => $checklistCategoria]));
    $trail->push('Editar Vinculo da Pergunta');
});

/**
 * Checklist / Domínios
 */
Breadcrumbs::for('admin.core.checklist.dominios.index', function ($trail, $checklist) {
    $trail->push('Formulários para Aplicação', route('admin.core.checklist.index'));
    $trail->push($checklist->nome);
    $trail->push('Domínios');
});

Breadcrumbs::for('admin.core.checklist.dominios.create', function ($trail, $checklist) {
    $trail->push('Formulários para Aplicação', route('admin.core.checklist.index'));
    $trail->push($checklist->nome);
    $trail->push('Domínios');
});

/**
 * Checklist / Categorias
 */

Breadcrumbs::for('admin.core.checklist.categorias.index', function ($trail, $checklist) {
    $trail->push('Formulários para Aplicação', route('admin.core.checklist.index'));
    $trail->push($checklist->nome);
    $trail->push('Categorias');
});

Breadcrumbs::for('admin.core.checklist.categorias.create', function ($trail, $checklist) {
    $trail->push('Formulários para Aplicação', route('admin.core.checklist.index'));
    $trail->push($checklist->nome);
    $trail->push('Categorias');
});

Breadcrumbs::for('admin.core.checklist.view', function ($trail, $id) {
    $trail->parent('admin.core.checklist.index');
    $trail->push('Formulário', route('admin.core.checklist.view', $id));
});

/**
 * Formulário - Unidade Produtiva
 */

Breadcrumbs::for('admin.core.checklist_unidade_produtiva.index', function ($trail) {
    $trail->push('Formulários', route('admin.core.checklist_unidade_produtiva.index'));
});
Breadcrumbs::for('admin.core.checklist_unidade_produtiva.excluidos', function ($trail) {
    $trail->push('Formulários', route('admin.core.checklist_unidade_produtiva.index'));
});
Breadcrumbs::for('admin.core.checklist_unidade_produtiva.create', function ($trail, $checklist, $produtor, $unidadeProdutiva) {
    $trail->parent('admin.core.checklist_unidade_produtiva.index');
    $trail->push('Aplicar Formulário - Modelo: ' . $checklist->nome, null);
});
Breadcrumbs::for('admin.core.checklist_unidade_produtiva.edit', function ($trail, $id) {
    $trail->parent('admin.core.checklist_unidade_produtiva.index');
    $trail->push('Aplicar Formulário', route('admin.core.checklist_unidade_produtiva.edit', $id));
});
Breadcrumbs::for('admin.core.checklist_unidade_produtiva.view', function ($trail, $id) {
    $trail->parent('admin.core.checklist_unidade_produtiva.index');
    $trail->push('Visualizar Formulário', route('admin.core.checklist_unidade_produtiva.view', $id));
});
Breadcrumbs::for('admin.core.checklist_unidade_produtiva.pdf', function ($trail, $id) {
    $trail->parent('admin.core.checklist_unidade_produtiva.index');
    $trail->push('Visualizar Formulário', route('admin.core.checklist_unidade_produtiva.view', $id));
});
Breadcrumbs::for('admin.core.checklist_unidade_produtiva.template', function ($trail) {
    $trail->parent('admin.core.checklist_unidade_produtiva.index');
    $trail->push('Escolher Formulário', route('admin.core.checklist_unidade_produtiva.template'));
});
Breadcrumbs::for('admin.core.checklist_unidade_produtiva.produtor_unidade_produtiva', function ($trail, $id) {
    $trail->parent('admin.core.checklist_unidade_produtiva.index');
    $trail->push('Escolher Formulário', route('admin.core.checklist_unidade_produtiva.template'));
    $trail->push('Escolher Unidade Produtiva', route('admin.core.checklist_unidade_produtiva.view', $id));
});
Breadcrumbs::for('admin.core.checklist_unidade_produtiva.compare', function ($trail) {
    $trail->parent('admin.core.checklist_unidade_produtiva.index');
    $trail->push('Comparar');
});
Breadcrumbs::for('admin.core.checklist_unidade_produtiva.compareView', function ($trail) {
    $trail->parent('admin.core.checklist_unidade_produtiva.index');
    $trail->push('Comparar');
});
Breadcrumbs::for('admin.core.checklist_unidade_produtiva.analiseIndex', function ($trail) {
    $trail->parent('admin.core.checklist_unidade_produtiva.index');
    $trail->push('Análise de Formulários');
});

/**
 * Plano Ação - Unidade Produtiva
 */
Breadcrumbs::for('admin.core.plano_acao.index', function ($trail) {
    $trail->push('Plano de Ação', route('admin.core.plano_acao.index'));
});
Breadcrumbs::for('admin.core.plano_acao.excluidos', function ($trail) {
    $trail->push('Plano de Ação', route('admin.core.plano_acao.index'));
});

Breadcrumbs::for('admin.core.plano_acao.create', function ($trail) {
    $trail->parent('admin.core.plano_acao.index');
    $trail->push('Criar Plano de Ação', null);
});
Breadcrumbs::for('admin.core.plano_acao.edit', function ($trail, $id) {
    $trail->parent('admin.core.plano_acao.index');
    $trail->push('Editar Plano de Ação', route('admin.core.plano_acao.edit', $id));
});
Breadcrumbs::for('admin.core.plano_acao.view', function ($trail, $id) {
    $trail->parent('admin.core.plano_acao.index');
    $trail->push('Visualizar Plano de Ação', route('admin.core.plano_acao.view', $id));
});
Breadcrumbs::for('admin.core.plano_acao.produtor_unidade_produtiva', function ($trail) {
    $trail->parent('admin.core.plano_acao.index');
    $trail->push('Escolher Unidade Produtiva', route('admin.core.plano_acao.index'));
});

/**
 * Plano Ação - Com Checklist
 */
Breadcrumbs::for('admin.core.plano_acao.checklist_unidade_produtiva', function ($trail) {
    $trail->parent('admin.core.plano_acao.index');
    $trail->push('Escolher Formulário Aplicado', route('admin.core.plano_acao.index'));
});
Breadcrumbs::for('admin.core.plano_acao.create_com_checklist', function ($trail) {
    $trail->parent('admin.core.plano_acao.index');
    $trail->push('Criar Plano de Ação com Formulário Aplicado', null);
});
Breadcrumbs::for('admin.core.plano_acao.edit_com_checklist', function ($trail) {
    $trail->parent('admin.core.plano_acao.index');
    $trail->push('Editar Plano de Ação com Formulário Aplicado', null);
});
Breadcrumbs::for('admin.core.plano_acao.pdf', function ($trail, $id) {
    $trail->parent('admin.core.plano_acao.index');
});

/**
 * Plano Ação - Coletivo
 */
Breadcrumbs::for('admin.core.plano_acao_coletivo.index', function ($trail) {
    $trail->push('Plano de Ação Coletivo', route('admin.core.plano_acao_coletivo.index'));
});
Breadcrumbs::for('admin.core.plano_acao_coletivo.excluidos', function ($trail) {
    $trail->push('Plano de Ação Coletivo', route('admin.core.plano_acao_coletivo.index'));
});
Breadcrumbs::for('admin.core.plano_acao_coletivo.create', function ($trail) {
    $trail->parent('admin.core.plano_acao_coletivo.index');
    $trail->push('Criar Plano de Ação Coletivo', null);
});
Breadcrumbs::for('admin.core.plano_acao_coletivo.edit', function ($trail, $id) {
    $trail->parent('admin.core.plano_acao_coletivo.index');
    $trail->push('Editar Plano de Ação Coletivo', route('admin.core.plano_acao_coletivo.edit', $id));
});
Breadcrumbs::for('admin.core.plano_acao_coletivo.view', function ($trail, $id) {
    $trail->parent('admin.core.plano_acao_coletivo.index');
    $trail->push('Visualizar Plano de Ação Coletivo', route('admin.core.plano_acao_coletivo.view', $id));
});
Breadcrumbs::for('admin.core.plano_acao_coletivo.unidade_produtiva.index', function ($trail, $id) {
    $trail->parent('admin.core.plano_acao_coletivo.index');
});

/**
 * Dados
 */
Breadcrumbs::for('admin.core.dado.index', function ($trail) {
    $trail->push('Sampa+Rural', route('admin.core.dado.index'));
});
Breadcrumbs::for('admin.core.dado.create', function ($trail) {
    $trail->parent('admin.core.dado.index');
    $trail->push('Criar Sampa+Rural', null);
});
Breadcrumbs::for('admin.core.dado.edit', function ($trail, $id) {
    $trail->parent('admin.core.dado.index');
    $trail->push('Editar Sampa+Rural', route('admin.core.dado.edit', $id));
});
Breadcrumbs::for('admin.core.dado.view', function ($trail, $id) {
    $trail->parent('admin.core.dado.index');
    $trail->push('Dados de acesso', route('admin.core.dado.view', $id));
});

/**
 *  Logs
 */
Breadcrumbs::for('admin.core.logs.index', function ($trail) {
    $trail->push('Logs', route('admin.core.logs.index'));
});

/**
 *  Sobre
 */
Breadcrumbs::for('admin.core.sobre.index', function ($trail) {
    $trail->push('Sobre', route('admin.core.sobre.index'));
});

Breadcrumbs::for('admin.core.sobre.edit', function ($trail, $id) {
    $trail->parent('admin.core.sobre.index');
    $trail->push('Editar Sobre', route('admin.core.sobre.edit', $id));
});

/**
 * Mapa / Relatórios / Indicadores
 */
Breadcrumbs::for('admin.core.mapa.index', function ($trail) {
    $trail->push('Mapa', route('admin.core.mapa.index'));
});

Breadcrumbs::for('admin.core.report.index', function ($trail) {
    $trail->push('Download CSV', route('admin.core.report.index'));
});

Breadcrumbs::for('admin.core.indicadores.index', function ($trail) {
    $trail->push('Indicadores', route('admin.core.indicadores.index'));
});

require __DIR__ . '/auth.php';
require __DIR__ . '/log-viewer.php';
