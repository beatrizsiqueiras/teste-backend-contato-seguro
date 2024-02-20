<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;
use Contatoseguro\TesteBackend\Enum\LogActions;
use DateTime;

class ProductLogService
{
    private \PDO $pdo;
    private AdminUserService $adminUserService;

    public function __construct()
    {
        $this->pdo = DB::connect();
        $this->adminUserService = new AdminUserService();

    }

    public function getAll()
    {
        $query = "
            SELECT *
            FROM product_log
        ";

        $stm = $this->pdo->prepare($query);
        $stm->execute();

        return $stm;
    }


    public function getLogsByProductId($productId)
    {
        $stm = $this->pdo->prepare("
            SELECT *
            FROM product_log
            WHERE product_id = {$productId}
        ");

        $stm->execute();

        return $stm;
    }


    public function insertOne($productId, $adminUserId, LogActions $action)
    {
        $actionValue = $action->value;
        $query = "INSERT INTO product_log (
            product_id,
            admin_user_id,
            action
        ) VALUES (
            {$productId},
            {$adminUserId},
            '{$actionValue}'
        )";
        $stm = $this->pdo->prepare($query);

        return $stm->execute();
    }

    public function generateProductLogsString(int $productId): string // service
    {
        $productLogString = '';
        $productLogs = $this->getLogsByProductId($productId)->fetchAll();

        if (empty($productLogs)) {
            return 'Logs não encontrados';
        }

        foreach ($productLogs as $log) {
            $logUser = $this->adminUserService->getOne($log->admin_user_id)->fetch();
            $logUserName = !empty($logUser) ? ucfirst($logUser->name) : "Usuário não encontrado (Id: $log->admin_user_id)";

            $logAction = get_translated_log_action($log->action);
            
            $logDate = DateTime::createFromFormat('Y-m-d H:i:s', $log->timestamp)->format('d/m/Y H:i:s');

            $productLogString .= "($logUserName, $logAction, $logDate), ";
        }

        return $productLogString;
    }
}
