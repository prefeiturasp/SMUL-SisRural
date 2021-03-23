<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class ChecklistStatusFlowEnum extends Enum implements LocalizedEnum
{
    const AguardandoRevisao = 'aguardando_revisao';
    const Aprovado = 'aprovado';
    const Reprovado = 'reprovado';
};
