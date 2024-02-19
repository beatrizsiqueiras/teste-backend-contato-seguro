<?php

use Contatoseguro\TesteBackend\Enum\LogActions;

function match_log_action_enum(string $action)
{
    switch ($action) {
        case "create":
            return LogActions::Create;
            break;

        case "update":
            return LogActions::Update;
            break;

        case "delete":
            return LogActions::Delete;
            break;

        default:
            return LogActions::Delete;
            break;
    }
}

function get_translated_log_action(string $action)
{
    $translatedAction = '';
    $actionEnum = match_log_action_enum($action);

    switch ($actionEnum) {
        case LogActions::Create:
            $translatedAction = "Criação";
            break;

        case LogActions::Update:
            $translatedAction = "Atualização";
            break;

        case LogActions::Delete:
            $translatedAction = "Remoção";
            break;

        default:
            $translatedAction = "Ação não reconhecida";
            break;
    }

    return $translatedAction;
}
