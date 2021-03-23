<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class TipoPontuacaoEnum extends Enum implements LocalizedEnum
{
    const SemPontuacao = 'sem_pontuacao';
    const ComPontuacao = 'com_pontuacao';
    const ComPontuacaoFormulaPersonalizada = 'com_pontuacao_formula_personalizada';
}
