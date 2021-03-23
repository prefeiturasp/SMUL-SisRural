<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TemplateChecklistStatusEnum extends Enum
{
    const Rascunho = 'rascunho';
    const Inativo = 'inativo';
    const Publicado = 'publicado';
}
