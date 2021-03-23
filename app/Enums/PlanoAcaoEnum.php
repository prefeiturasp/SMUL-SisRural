<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class PlanoAcaoEnum extends Enum implements LocalizedEnum
{
    const Opcional = 'opcional';
    const Obrigatorio = 'obrigatorio';
    const NaoCriar = 'nao_criar';
}
