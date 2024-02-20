<?php

namespace ContatoSeguro\TesteBackend\Model;

use ContatoSeguro\TesteBackend\Enum\FilterTypes;

class AllowedFilter
{
    public function __construct(
        public string $columnName,
        public FilterTypes $type = FilterTypes::Default,
    ) {
    }
}
