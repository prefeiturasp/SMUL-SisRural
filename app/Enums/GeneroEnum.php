<?php
    namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

    final class GeneroEnum extends Enum implements LocalizedEnum
    {
        const Feminino = 'feminino';
        const Masculino = 'masculino';
        const Outro = 'outro';
        const Nao_deseja_informar = 'na';
    }
?>
