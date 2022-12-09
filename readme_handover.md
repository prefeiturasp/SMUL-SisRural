# SisRural

-   Model
-   Controller
-   DataTable
-   Policy
-   Repository
-   view/blade

# Domínio

http://localhost:8000/admin/dominio

-   Contém N Unidades Operacionais
-   Possuí restrições de abrangência
-   Abrangência será utilizada para vincular produtores no Domínio/Unidade Operacional
-   Abrangência: Estadual, Municipal, Região
-   Região = KML
-   Usuário Administrador pode cadastrar/editar Domínios

# Unidade Operacional

http://localhost:8000/admin/unidade_operacional

-   Faz parte de um Domínio
-   Domínio determina restrições da abrangência
-   Contém N Unidades Produtivas
-   Usuário Domínio pode cadastrar/editar Unidades Operacionais (Do seu domínio)

# Abrangência (Lat/Lng vs Unidades Produtivas)

-   Ao salvar uma Unidade Produtiva ou Unidade Operacional

    -   vai extrair as abrangências definidas
    -   varrer as unidades produtivas (Lat/Lng)
    -   testar se a lat/lng faz parte da Cidade/Estado/Região (teste Lat/Lng vs Poligno)

-   Arquivo que relaciona as unidades operacionais com as unidades produtivas (UnidadeOperacionalService)
    //- Arquivo que relacionao as unidades produtivas com as unidades operacionais (UnidadeProdutivaService)

## Regioes (Kml)

http://localhost:8000/admin/regiao

-   Recebe um arquivo KML
-   Ele pode ser gerado no link http://www.birdtheme.org/useful/v3tool.html (Explicação no README.MD)

# Produtores/as

http://localhost:8000/admin/produtor

-   Produtor pode ter N unidades produtivas
-   São criados/editados por usuários do tipo Técnicos e Unidades Operacionais
-   São visualizados por usuários do tipo Domínio

# Cadastro Rápido (Produtor + Unidade Produtiva)

http://localhost:8000/admin/novo_produtor_unidade_produtiva/create

-   Cadastro rápido do produtor vinculando com uma unidade produtiva (nova ou existente)
-   Ao salvar, Verifica a Lat/Lng para saber se esta na Abrangência da Unidade Operacional do Usuário logado

# Unidades Produtivas

http://localhost:8000/admin/unidade_produtiva

-   Possuí alguns relacionamentos N como (Certificação, Canal Comercialização, Esgotamento Sanitário, Pressão Social, Resíduos Sólidos, Contaminação Água, Tipo Fontes Água)
-   Ao salvar, Verifica a Lat/Lng para saber se esta na Abrangência da Unidade Operacional do Usuário logado
-   Pessoas / Uso do Solo / Infra-Estrutura, bloco avulso, relacionamento belongsTo, save relaciona com a unidade_produtiva_id
-   Arquivos (UnidadeProdutivaArquivoRepository), os arquivos publicados no FILESYSTEM_DRIVER definido.

---

RESUMO Domínio, Unidade Operacional, Abrangências, Produtores/as, Unidades Produtivas

---

# Caderno de Campo

http://localhost:8000/admin/cadernos

## Caderno de Campo Base (Template)

http://localhost:8000/admin/templates_caderno

http://localhost:8000/admin/template_perguntas

-   Template faz parte de um Domínio
-   Usuário tipo Domínio precisa criar um Template de Caderno
-   Template contém N perguntas
-   Perguntas podem ser utilizadas em N Templates
-   Perguntas tipo Texto / Uma Resposta / Multiplas Respostas

-   Pergunta é relacionada com o template (tabela template_pergunta_templates)

    -   Pergunta pode ser removida de um Template
    -   Respostas para aquela pergunta permanecem no Caderno de Campo Aplicado
    -   Ao visualizar o caderno de campo aplicado com uma pergunta removida, a resposta é visualizada

-   PDF p/ testar, precisa estar rodando em um servidor (Apache/Ng)

## Aplicação do Caderno de Campo

http://localhost:8000/admin/cadernos/produtor_unidade_produtiva

-   Produtor/Unidade Produtiva vinculado no caderno aplicado
-   Aplicado por um usuário do tipo Unidade Operacional/Técnico
-   P/ aplicar um caderno de campo o Domínio precisa ter um Template cadastrado
-   Caderno de campo finalizado não pode mais ser editado (CadernoPolicy.php)

# Formulário

http://localhost:8000/admin/checklist_unidade_produtiva

## Formulário Base (Template)

http://localhost:8000/admin/checklist

http://localhost:8000/admin/perguntas

-   Formulário pertence a um Domínio
-   Formulário contem N Categorias, cada Categoria contém N Perguntas
-   Primeiro cadastro de Perguntas, depois do Template do Formulário

-   Tipos de Perguntas: Semafórica / Binária / Numérica / Texto / Tabela / Multipla Escolha / Escolha Simples / Anexo / Alguns tipos possuem pontuação, outros não
-   Uma pergunta pode ser utilizada em mais de um formulário
-   Resposta da pergunta fica vinculada a Unidade Produtiva. Caso ela responda outro formulário que possuí a mesma pergunta, a resposta já vai vir preenchida (unidade_produtiva_respostas vs checklist_snapshot_respostas)

-   Outros Domínios/Unidades Operacionais/Técnicos podem aplicar o formulário
-   Fluxo de Aprovação
-   Plano de Ação (Não tem, Opcional, Obrigatório)
-   Pontuação - Perguntas com cor "cinza" não entram no calculo
-   Perguntas podem ser obrigatórias ou não (verificado no momento de salvar a aplicação do formulário)
-   Status Rascunho / Inativo / Publicado

    -   Rascunho: Usuário ainda esta criando o template
    -   Inativo: Técnicos não podem mais aplicar o template do formulário
    -   Publicado: Técnicos podem aplicar o template do formulário

## Aplicação de um Formulário

http://localhost:8000/admin/checklist_unidade_produtiva/template

-   Usuário que vai aplicar seleciona o Template desejado
-   Vinculado a um Produtor / Unidade Produtiva
-   Pergunta já foi respondida pelo produtor/unidade produtiva, a resposta já vem preenchida
-   Status Rascunho p/ continuar respondendo depois
-   Status Finalizado, conclui a aplicação

/

-   Se o Status é Rascunho, respostas ficam na Unidade Produtiva (tabela unidade_produtiva_respostas)
-   Se o Status é Finalizado, respostas ficam na Aplicação (tabela checklist_snapshot_respostas)
-   Se possuí "fluxo de aprovação", é disparado email para os aprovadores. (ver ChecklistUnidadeProdutivaObserver)
-   Eles podem aprovar/reprovar/retornar p/ novos incrementos do Técnico
-   Se possuí "plano de ação", mostra um alerta pergunta se o usuário quer criar o Plano de Ação
-   Caso o "plano de ação" seja obrigatório, o Formulário fica "aguardando" a criação do PDA antes de "Finalizar" ele.

-   Sessão para análise, onde aparecem os formulários que precisam ser analisados
-   Calculo da Pontuação (ChecklistUnidadeProdutivaScore.php)

# Plano de Ação

http://localhost:8000/admin/plano_acao
http://localhost:8000/admin/plano_acao_coletivo

-   Pode ser aplicado a um Produtor/Unidade Produtiva (Individual), através de um Formulário Aplicado ou Coletivo (N Unidades Produtivas)
-   Possuí N Ações
-   Cada Ação pode ter N acompanhamentos
-   Plano de ação pode ter N acompanhamentos
-   Ao alterar o status/nome/prazo de um PDA , gera um acompanhamento no PDA
-   Ao alterar o status de uma ação, gera um acompanhamento na Ação do PDA

## Tipo: Individual

-   Plano de ação aplicado diretamente a um Produtor / Unidade Produtiva
-   Produtor/Unidade Produtiva só pode ter um "em andamento", após concluído, é possível abrir um novo plano de ação

## Tipo: Formulário

-   Plano de ação aplicado através de "Formulário Aplicado"
-   As ações são geradas através das "perguntas" vinculadas no formulário.
-   Se a resposta da pergunta tiver "cor" e for "verde", a "Prioridade" da ação fica como "Atendida"
-   No cadastro do formulário, é informado se a "pergunta" vai para o plano de ação e qual é a 'Ação Recomendada'

## Tipo: Coletivo

-   Plano de ação coletivo, N Produtores/as x Unidades Produtivas
-   N Ações
-   Para cada produtor é gerado um Plano de Ação vinculado ao Plano de Ação Pai
-   Para cada PDA do produtor, é gerado as ações previamente cadastradas no Plano de Ação Pai
-   Ao alterar o "status" de uma ação, todas as ações "filhas" são atualizadas

# Permission Scope

-   O que determina se o usuário pode ou não ver o registro?
-   Como fica o tratamento das listagens?
-   Tudo através do Permission Scope definido no "boot" do Model.
-   Ex: Só posso ver uma Unidade Produtiva (Regra: UnidadeProdutivaPermissionScope.php) se ela esta contida na Unidade Operacional do usuário logado.

# Dados Sampa+Rural (API)

http://localhost:8000/admin/dado

-   Cadastro de "domínios" que liberam as unidades produtivas das abrangências determinadas
-   No cadastro é definido uma "chave", que deve ser informada na requisição da API.
-   Após salvo a chave é criptografada e não é possível recuperar.
-   API retorna as unidades produtivas/produtores da abrangência informada
-   Configuração da api (config/auth.php - api_dados)
-   Requisição: (Enviar POSTMAN)
    -   URL http://localhost:8000/api/dados/unidades_produtivas
    -   Header: Authorization Bearer chave_informada_no_cadastro
    -   Body: form-data, key, page = 1

# Mapa

http://localhost:8000/admin/mapa

# Download CSV

http://localhost:8000/admin/report

# Indicadores

http://localhost:8000/admin/indicadores

# Logs

http://localhost:8000/admin/logs

# API Offline (APP)

//TODO: Gerar POSTMAN das requisições

# Usuários

http://localhost:8000/admin/auth/user

## Roles

-   Tipos

    -   Super Administrador (Não utilizar este usuário, ignora os Policies do sistema)
    -   Administrador
    -   Domínio
    -   Unidade Operacional
    -   Técnico

-   Hierarquia

    -   Administrador > Dominio > Unidade Operacional > Técnico
    -   Administrador cadastram Domínios
    -   Domínios cadastram Unidades Operacionais
    -   Unidades Operacionais cadastradam Técnicos

-   Um usuário pode ter N role (papel)

    -   Cada "role" do usuário é um usuário distinto na base de dados
    -   Se o usuário é do "Dominio Ater" e "Domínio PSA", teremos dois usuários cadastrados no banco. Cada usuário vinculado com seu respectivo domínio.
    -   Caso dados do usuário seja atualizado (nome, email, senha ...), essas informações são propagadas para todos os usuários com o mesmo CPF

-   No header do CMS é possível alterar a "role" do usuário atual
-   Um usuário pode ter N permissões (ver PermissionRoleTableSeeder.php). Permissões geralmente são consumidas na .blade.php ou Policy.php

## Termos de Uso

-   Usuário precisa aceitar os termos de uso para acessar o sistema
-   Middleware EnsureTermsIsAccepted.php
