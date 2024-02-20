<?php

namespace ContatoSeguro\TesteBackend\Service;

use ContatoSeguro\TesteBackend\Config\DB;
use ContatoSeguro\TesteBackend\Enum\FilterTypes;
use ContatoSeguro\TesteBackend\Enum\LogActions;
use ContatoSeguro\TesteBackend\Model\AllowedFilter;
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

    public function getAll(array $queryParams = [])
    {
        $filtersQuery = get_filters_query($queryParams, [
            'adminUserId' => new AllowedFilter('pl.admin_user_id'),
            'productId' => new AllowedFilter('pl.product_id'),
            'action' => new AllowedFilter('pl.action', FilterTypes::String),
            'updatedField' => new AllowedFilter('pl.after', FilterTypes::CompareString),
            'createdAt' => new AllowedFilter('pl.created_at', FilterTypes::Date)
        ]);

        $query = "
            SELECT 
                pl.id as logId,
                pl.admin_user_id AS userId,
                au.name AS userName,
                pl.product_id AS productId,
                p.title AS productName,
                pl.action,
                pl.before,
                pl.after,
                pl.created_at as logDate
            FROM product_log AS pl
            INNER JOIN admin_user AS au ON au.id = pl.admin_user_id
            INNER JOIN product as p ON p.id = pl.product_id
            WHERE pl.deleted_at IS NULL
            $filtersQuery ;
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

    public function insertOne(string $productId, string $adminUserId, LogActions $action, string $before = '', string $after = '')
    {
        $actionValue = $action->value;
        $query = "INSERT INTO product_log (
            product_id,
            admin_user_id,
            action,
            before,
            after
        ) VALUES (
            {$productId},
            {$adminUserId},
            '{$actionValue}',
            '{$before}',
            '{$after}'
        )";
        $stm = $this->pdo->prepare($query);

        return $stm->execute();
    }

    public function generateProductLogsString(int $productId): string
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

            $logDate = DateTime::createFromFormat('Y-m-d H:i:s', $log->created_at)->format('d/m/Y H:i:s');

            $productLogString .= "($logUserName, $logAction, $logDate), ";
        }

        return $productLogString;
    }
}
