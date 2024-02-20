<?php

namespace ContatoSeguro\TesteBackend\Enum;

enum LogActions: string
{
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';
}
