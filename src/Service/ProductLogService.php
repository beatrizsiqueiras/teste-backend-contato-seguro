<?php

namespace ContatoSeguro\TesteBackend\Service;

use ContatoSeguro\TesteBackend\Config\DB;
use ContatoSeguro\TesteBackend\Enum\FilterTypes;
use ContatoSeguro\TesteBackend\Enum\LogActions;
use ContatoSeguro\TesteBackend\Model\AdminUser;
use ContatoSeguro\TesteBackend\Helper\Filter\AllowedFilter;
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

    public function getAll(array $queryParams = []): array
    {
        try {
            $filtersQuery = get_filters_query($queryParams, [
                'adminUserId' => new AllowedFilter('pl.admin_user_id'),
                'productId' => new AllowedFilter('pl.product_id'),
                'action' => new AllowedFilter('pl.action', FilterTypes::String),
                'updatedField' => new AllowedFilter('pl.after', FilterTypes::CompareString),
                'createdAt' => new AllowedFilter('pl.created_at', FilterTypes::Date)
            ]);

            $query = "
            SELECT
                pl.id AS logId,
                pl.admin_user_id AS userId,
                au.name AS userName,
                pl.product_id AS productId,
                p.title AS productName,
                pl.action,
                pl.before,
                pl.after,
                pl.created_at AS logDate
            FROM
                product_log AS pl
            INNER JOIN admin_user AS au ON
                au.id = pl.admin_user_id
            INNER JOIN product AS p ON
                p.id = pl.product_id
            WHERE
                pl.deleted_at IS NULL
                $filtersQuery ;
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return [];
        }
    }

    public function getLogsByProductId($productId): array
    {
        try {
            $query = "
            SELECT 
                *
            FROM 
                product_log
            WHERE 
                product_id = :productId
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":productId", $productId, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return [];
        }
    }

    public function insertOne(string $productId, string $adminUserId, LogActions $action, string $before = '', string $after = ''): bool
    {
        try {
            $action = $action->value;

            $query = "
            INSERT INTO product_log (
                product_id,
                admin_user_id,
                action,
                before,
                after
            )
            VALUES (
                :product_id, 
                :admin_user_id, 
                :action, :before, 
                :after
            )";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":product_id", $productId, \PDO::PARAM_INT);
            $stmt->bindParam(":admin_user_id", $adminUserId, \PDO::PARAM_INT);
            $stmt->bindParam(":action", $action, \PDO::PARAM_STR);
            $stmt->bindParam(":before", $after, \PDO::PARAM_STR);
            $stmt->bindParam(":after", $before, \PDO::PARAM_STR);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return false;
        }
    }

    public function generateProductLogsString(int $productId): string
    {
        $productLogs = $this->getLogsByProductId($productId);

        if (empty($productLogs)) {
            return 'Logs não encontrados';
        }

        $logStrings = [];
        foreach ($productLogs as $log) {
            $stmtt = $this->adminUserService->getOne($log['admin_user_id']);
            $logUser = AdminUser::hydrateByFetch($stmtt->fetch());

            $logUserName = !empty($logUser) ? ucfirst($logUser->name) : "Usuário não encontrado (Id: {$log['admin_user_id']})";
            $logAction = get_translated_log_action($log['action']);
            $logDate = DateTime::createFromFormat('Y-m-d H:i:s', $log['created_at'])->format('d/m/Y H:i:s');

            $logStrings[] = "($logUserName, $logAction, $logDate)";
        }

        return implode(", ", $logStrings);
    }
}
