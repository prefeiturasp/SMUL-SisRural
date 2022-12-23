<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class RolesEnum extends Enum implements LocalizedEnum
{
    const Admin = 'Administrator';
    const Dominio = 'Dominio';
    const UnidOperacional = 'Unidade Operacional';
    const Tecnico = 'Técnico/a';
    const TecnicoExterno = 'Técnico/a Externo/a';
}
