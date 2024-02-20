<?php

namespace ContatoSeguro\TesteBackend\Enum;

enum FilterTypes: string
{
    case Date = 'date';
    case CompareString = 'compareString';
    case String = 'string';
    case Default = 'default';
}
