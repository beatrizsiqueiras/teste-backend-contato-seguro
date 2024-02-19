<?php

use Contatoseguro\TesteBackend\Enum\LogActions;

function get_translated_log_action(string $action): string
{
    switch ($action) {
        case LogActions::Create:
            return "Criação";
        case LogActions::Update:
            return "Atualização";
        case LogActions::Delete:
            return "Remoção";
        default:
            return "Ação não reconhecida";
    }
}
