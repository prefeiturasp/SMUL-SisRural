<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class ProdutorUnidadeProdutivaStatusEnum extends Enum implements LocalizedEnum
{
    const Ativo = 'ativo';
    const Inativo = 'inativo';
}
