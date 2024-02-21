<?php

namespace ContatoSeguro\TesteBackend\Enum;

enum FilterTypes: string
{
    case Date = 'date';
    case Number = 'number';
    case String = 'string';
    case PartialString = 'partialString';
}
