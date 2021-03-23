<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class CheckboxEnum extends Enum implements LocalizedEnum
{
    const SemResposta = null;
    const Sim = '1';
    const Nao = '0';
}
