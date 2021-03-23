<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class PlanoAcaoItemStatusEnum extends Enum implements LocalizedEnum
{
    const NaoIniciado = 'nao_iniciado';
    const EmAndamento = 'em_andamento';
    const Concluido = 'concluido';
    const Cancelado = 'cancelado';
}
