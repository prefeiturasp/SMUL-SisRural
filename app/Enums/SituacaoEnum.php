<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class SituacaoEnum extends Enum implements LocalizedEnum
{
    const Ativa = '0';
    const Arquivada = '1';
}
