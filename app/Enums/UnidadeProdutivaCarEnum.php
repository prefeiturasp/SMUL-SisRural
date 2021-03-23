<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UnidadeProdutivaCarEnum extends Enum
{
    const Sem_Resposta = null;
    const Sim = 'sim';
    const Nao = 'nao';
    const Nao_se_aplica = 'nao_se_aplica';
}
