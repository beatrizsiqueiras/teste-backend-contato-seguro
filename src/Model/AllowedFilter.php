<?php

namespace Contatoseguro\TesteBackend\Model;

use Contatoseguro\TesteBackend\Enum\FilterTypes;

class AllowedFilter
{
    public function __construct(
        public string $columnName,
        public FilterTypes $type = FilterTypes::Default,
    ) {
    }
}
