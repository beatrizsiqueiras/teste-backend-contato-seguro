<?php

namespace Contatoseguro\TesteBackend\Enum;

enum LogActions: string
{
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';
}
