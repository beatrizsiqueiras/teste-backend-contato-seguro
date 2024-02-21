<?php

namespace ContatoSeguro\TesteBackend\Helper\Sort;

use ContatoSeguro\TesteBackend\Enum\SortDirection;

class AllowedSort
{
    public function __construct(
        public string $columnName
    ) {
    }
}
