<?php

use App\Enums\BooleanEnum;
use App\Enums\CheckboxEnum;
use App\Enums\ChecklistStatusEnum;
use App\Enums\ChecklistStatusFlowEnum;
use App\Enums\GeneroEnum;
use App\Enums\TipoPerguntaEnum;
use App\Enums\TipoTemplatePerguntaEnum;
use App\Enums\PlanoAcaoClassificacaoEnum;
use App\Enums\PlanoAcaoEnum;
use App\Enums\PlanoAcaoItemStatusEnum;
use App\Enums\PlanoAcaoPrioridadeEnum;
use App\Enums\PlanoAcaoStatusEnum;
use App\Enums\ProcessaProducaoEnum;
use App\Enums\ProdutorUnidadeProdutivaStatusEnum;
use App\Enums\RolesEnum;
use App\Enums\SituacaoEnum;
use App\Enums\TipoPontuacaoEnum;
use App\Enums\UnidadeProdutivaCarEnum;

return [

    TipoPerguntaEnum::class => [
        TipoPerguntaEnum::Semaforica => 'Semafórica',
        TipoPerguntaEnum::SemaforicaCinza => 'Semafórica - com não se aplica',

        TipoPerguntaEnum::Binaria => 'Binária',
        TipoPerguntaEnum::BinariaCinza => 'Binária - com não se aplica',

        TipoPerguntaEnum::Numerica => 'Numérica sem pontuação',
        TipoPerguntaEnum::NumericaPontuacao => 'Numérica com pontuação',

        TipoPerguntaEnum::Texto => 'Texto',
        TipoPerguntaEnum::Tabela => 'Tabela',

        TipoPerguntaEnum::MultiplaEscolha => 'Multipla-escolha',

        TipoPerguntaEnum::EscolhaSimples => 'Escolha simples',
        TipoPerguntaEnum::EscolhaSimplesPontuacao => 'Escolha simples com pontuação',
        TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza => 'Escolha simples com pontuação - com \'não se aplica\' e/ou \'verde\'',

        TipoPerguntaEnum::Anexo => 'Anexo',

        TipoPerguntaEnum::Data => 'Data',
        TipoPerguntaEnum::Hora => 'Hora',
    ],

    GeneroEnum::class => [
        GeneroEnum::Feminino => 'Feminino',
        GeneroEnum::Masculino => 'Masculino',
        GeneroEnum::Outro => 'Outro',
        GeneroEnum::Nao_deseja_informar => 'Não desejo informar',
    ],

    TipoTemplatePerguntaEnum::class => [
        TipoTemplatePerguntaEnum::Text => 'Texto',
        TipoTemplatePerguntaEnum::Check => 'Uma Resposta',
        TipoTemplatePerguntaEnum::MultipleCheck => 'Múltiplas Respostas',
        TipoTemplatePerguntaEnum::Data => 'Data',
        TipoTemplatePerguntaEnum::Hora => 'Hora',
    ],

    PlanoAcaoClassificacaoEnum::class => [
        PlanoAcaoClassificacaoEnum::Prioritario => 'Prioritário',
        PlanoAcaoClassificacaoEnum::Recomendado => 'Recomendado',
        PlanoAcaoClassificacaoEnum::Atendido => 'Atendido',
    ],

    ChecklistStatusEnum::class => [
        ChecklistStatusEnum::Rascunho => 'Rascunho',
        ChecklistStatusEnum::AguardandoAprovacao => 'Aguardando Aprovação',
        ChecklistStatusEnum::Finalizado => 'Finalizado',
        ChecklistStatusEnum::AguardandoPda => 'Aguardando Plano de Ação',
    ],

    ChecklistStatusFlowEnum::class => [
        ChecklistStatusFlowEnum::Aprovado => 'Aprovado',
        ChecklistStatusFlowEnum::Reprovado => 'Reprovado',
        ChecklistStatusFlowEnum::AguardandoRevisao => 'Aguardando Revisao',
    ],

    PlanoAcaoEnum::class => [
        PlanoAcaoEnum::Opcional => 'Opcional',
        PlanoAcaoEnum::Obrigatorio => 'Obrigatório',
        PlanoAcaoEnum::NaoCriar => 'Não criar',
    ],

    PlanoAcaoPrioridadeEnum::class => [
        PlanoAcaoPrioridadeEnum::PriorizacaoTecnica => 'Priorização Técnica',
        PlanoAcaoPrioridadeEnum::AcaoRecomendada => 'Ação Recomendada',
        PlanoAcaoPrioridadeEnum::Atendida => 'Requisito Atendido',
    ],

    BooleanEnum::class => [
        BooleanEnum::Sim => 'Sim',
        BooleanEnum::Nao => 'Não',
    ],

    SituacaoEnum::class => [
        SituacaoEnum::Ativa => 'Ativa',
        SituacaoEnum::Arquivada => 'Arquivada',
    ],

    PlanoAcaoStatusEnum::class => [
        PlanoAcaoStatusEnum::NaoIniciado => 'Não Iniciado',
        PlanoAcaoStatusEnum::EmAndamento => 'Em Andamento',
        PlanoAcaoStatusEnum::Concluido => 'Concluído',
        PlanoAcaoStatusEnum::Cancelado => 'Cancelado'
    ],

    PlanoAcaoItemStatusEnum::class => [
        PlanoAcaoItemStatusEnum::NaoIniciado => 'Não Iniciado',
        PlanoAcaoItemStatusEnum::EmAndamento => 'Em Andamento',
        PlanoAcaoItemStatusEnum::Concluido => 'Concluído',
        PlanoAcaoItemStatusEnum::Cancelado => 'Cancelado',
    ],

    PlanoAcaoPrioridadeEnum::class => [
        PlanoAcaoPrioridadeEnum::PriorizacaoTecnica => 'Priorização Técnica',
        PlanoAcaoPrioridadeEnum::AcaoRecomendada => 'Ação Recomendada',
        PlanoAcaoPrioridadeEnum::Atendida => 'Atendida',
    ],

    TipoPontuacaoEnum::class => [
        TipoPontuacaoEnum::SemPontuacao => 'Sem pontuação - função de coleta de dados',
        TipoPontuacaoEnum::ComPontuacao => 'Calcular pontuação alcançada e nota percentual',
        TipoPontuacaoEnum::ComPontuacaoFormulaPersonalizada => 'Fórmula personalizada',
    ],

    RolesEnum::class => [
        RolesEnum::Admin => 'Administrador',
        RolesEnum::Dominio => 'Domínio',
        RolesEnum::UnidOperacional => 'Unidade Operacional',
        RolesEnum::Tecnico => 'Técnico/a',
        RolesEnum::TecnicoExterno => 'Técnico/a Externo/a'
    ],

    ProdutorUnidadeProdutivaStatusEnum::class => [
        ProdutorUnidadeProdutivaStatusEnum::Ativo => 'Ativo',
        ProdutorUnidadeProdutivaStatusEnum::Inativo => 'Inativo',
    ],

    CheckboxEnum::class => [
        CheckboxEnum::SemResposta => 'Sem resposta',
        CheckboxEnum::Sim => 'Sim',
        CheckboxEnum::Nao => 'Não',
    ],

    UnidadeProdutivaCarEnum::class => [
        UnidadeProdutivaCarEnum::Sem_Resposta => 'Sem Resposta',
        UnidadeProdutivaCarEnum::Sim => 'Sim',
        UnidadeProdutivaCarEnum::Nao => 'Não',
        UnidadeProdutivaCarEnum::Nao_se_aplica => 'Não se aplica',
    ],

    ProcessaProducaoEnum::class => [
        ProcessaProducaoEnum::Sim => 'Sim',
        ProcessaProducaoEnum::Nao => 'Não',
        ProcessaProducaoEnum::NaoTemInteresse => 'Não tem interesse'
    ]
];
