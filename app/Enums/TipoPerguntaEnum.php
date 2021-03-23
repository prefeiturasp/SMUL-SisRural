<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class TipoPerguntaEnum extends Enum implements LocalizedEnum
{
    const Semaforica = 'semaforica';
    const SemaforicaCinza = 'semaforica-cinza';

    const Binaria = 'binaria';
    const BinariaCinza = 'binaria-cinza';

    const NumericaPontuacao = 'numerica-pontuacao';
    const Numerica = 'numerica';

    const Texto = 'texto';
    const Tabela = 'tabela';

    const MultiplaEscolha = 'multipla-escolha';

    const EscolhaSimples = 'escolha-simples';
    const EscolhaSimplesPontuacao = 'escolha-simples-pontuacao';

    const EscolhaSimplesPontuacaoCinza = 'escolha-simples-pontuacao-cinza'; //Não se aplica

    const Anexo = 'anexo';
}
