<?php

use Contatoseguro\TesteBackend\Enum\LogActions;

function get_translated_log_action(string $action): string
{
    switch ($action) {
        case LogActions::Create->value:
            return "Criação";
        case LogActions::Update->value:
            return "Atualização";
        case LogActions::Delete->value:
            return "Remoção";
        default:
            return "Ação desconhecida";
    }
}
