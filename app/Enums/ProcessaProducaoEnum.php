<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class ProcessaProducaoEnum extends Enum implements LocalizedEnum
{
    const Sim = 'sim';
    const Nao = 'nao';
    const NaoTemInteresse = 'nao_tem_interesse';
}
