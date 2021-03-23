<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class ChecklistStatusEnum extends Enum implements LocalizedEnum
{
    const Rascunho = 'rascunho';
    const AguardandoPda = 'aguardando_pda';
    const AguardandoAprovacao = 'aguardando_aprovacao';
    const Finalizado = 'finalizado';
    const Cancelado = 'cancelado';
}
