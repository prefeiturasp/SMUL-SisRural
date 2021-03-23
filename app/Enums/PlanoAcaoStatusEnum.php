<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class PlanoAcaoStatusEnum extends Enum implements LocalizedEnum
{
    const Rascunho = 'rascunho';
    const AguardandoAprovacao = 'aguardando_aprovacao';
    const NaoIniciado = 'nao_iniciado';
    const EmAndamento = 'em_andamento';
    const Concluido = 'concluido';
    const Cancelado = 'cancelado';
}
