<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class PlanoAcaoPrioridadeEnum extends Enum implements LocalizedEnum
{
    const PriorizacaoTecnica = 'priorizacao_tecnica';
    const AcaoRecomendada = 'acao_recomendada';
    const Atendida = 'atendida';
}
