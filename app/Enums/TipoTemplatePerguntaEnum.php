<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class TipoTemplatePerguntaEnum extends Enum implements LocalizedEnum
{
    const Text = 'text';
    const Check = 'check';
    const MultipleCheck = 'multiple_check';
}
