<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class BooleanEnum extends Enum implements LocalizedEnum
{
    const Sim = '1';
    const Nao = '0';
}
