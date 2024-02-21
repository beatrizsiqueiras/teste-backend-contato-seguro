<?php

namespace ContatoSeguro\TesteBackend\Service;

use ContatoSeguro\TesteBackend\Config\DB;
use ContatoSeguro\TesteBackend\Enum\FilterTypes;
use ContatoSeguro\TesteBackend\Enum\LogActions;
use ContatoSeguro\TesteBackend\Model\AllowedFilter;
use stdClass;

class ProductService
{
    private \PDO $pdo;
    private ProductLogService $productLogService;
    private ProductCategoryService $productCategoryService;
    private AdminUserService $adminUserService;

    public function __construct()
    {
        $this->pdo = DB::connect();
        $this->productLogService = new ProductLogService();
        $this->productCategoryService = new ProductCategoryService();
        $this->adminUserService = new AdminUserService();
    }

    public function getAll(int $adminUserId, array $queryParams = []): array
    {
        $filtersQuery = get_filters_query($queryParams, [
            'createdAt' => new AllowedFilter('p.created_at', FilterTypes::Date),
            'active' => new AllowedFilter('p.active')
        ]);

        $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);

        $query = "
            SELECT p.*, c.title as category
            FROM product p
            INNER JOIN product_category pc ON pc.product_id = p.id
            INNER JOIN category c ON c.id = pc.category_id
            WHERE p.deleted_at IS NULL
            AND p.company_id = :companyId
            $filtersQuery
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':companyId', $companyId, \PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return [];
        }
    }

    public function getOne(int $id, int $adminUserId)
    {
        $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);

        $query = "
            SELECT *
            FROM product
            WHERE id = :id
            AND company_id = :companyId
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':companyId', $companyId, \PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt;
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return null;
        }
    }

    public function insertOne(array $body, string $adminUserId): bool
    {
        $this->pdo->beginTransaction();

        $query = "
            INSERT INTO product (
                company_id,
                title,
                price,
                active
            ) VALUES (
                :companyId,
                :title,
                :price,
                :active
            )
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':companyId', $body['company_id'], \PDO::PARAM_INT);
        $stmt->bindParam(':title', $body['title'], \PDO::PARAM_STR);
        $stmt->bindParam(':price', $body['price'], \PDO::PARAM_INT);
        $stmt->bindParam(':active', $body['active'], \PDO::PARAM_INT);

        try {
            $stmt->execute();
            $productId = $this->pdo->lastInsertId();

            $this->productCategoryService->insertOne($productId, $body['category_id']);
            $this->productLogService->insertOne(intval($productId), intval($adminUserId), LogActions::Create);
            $this->pdo->commit();

            return true;
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            $this->pdo->rollBack();

            return false;
        }
    }

    public function updateOne(int $id, array $body, string $adminUserId): bool
    {
        $previousProduct = $this->getOne($id, $adminUserId)->fetch(\PDO::FETCH_ASSOC);
        if (!$previousProduct) {
            return false;
        }
        $this->pdo->beginTransaction();

        $updatedAt = date('Y-m-d H:i:s');

        $query = "
            UPDATE product
            SET company_id = :companyId,
                title = :title,
                price = :price,
                active = :active,
                updated_at = :updatedAt
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':companyId', $body['company_id'], \PDO::PARAM_INT);
        $stmt->bindParam(':title', $body['title'], \PDO::PARAM_STR);
        $stmt->bindParam(':price', $body['price'], \PDO::PARAM_INT);
        $stmt->bindParam(':active', $body['active'], \PDO::PARAM_INT);
        $stmt->bindParam(':updatedAt', $updatedAt, \PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

        try {
            if (!$stmt->execute()) {
                return false;
            }

            $this->productCategoryService->updateOne($id, $body['category_id']);

            $updatedProduct = $this->getOne($id, $adminUserId)->fetch(\PDO::FETCH_ASSOC);

            if (!$updatedProduct) {
                return false;
            }

            [$productBefore, $produtcAfter] = $this->getPreviousAndUpdatedProductFields((object)$previousProduct, (object)$updatedProduct);

            $this->productLogService->insertOne(
                $id,
                $adminUserId,
                LogActions::Update,
                json_encode($productBefore),
                json_encode($produtcAfter)
            );

            $this->pdo->commit();

            return true;
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            $this->pdo->rollBack();

            return false;
        }
    }

    public function deleteOne(int $id, string $adminUserId): bool
    {
        $this->pdo->beginTransaction();

        $deletedAt = date('Y-m-d H:i:s');

        $query = "
            UPDATE product
            SET deleted_at = :deletedAt
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':deletedAt', $deletedAt, \PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

        try {
            if (!$stmt->execute()) {
                return false;
            }

            $this->productCategoryService->deleteOne($id);
            $this->productLogService->insertOne($id, $adminUserId, LogActions::Delete);
            $this->pdo->commit();

            return true;
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            $this->pdo->rollBack();

            return false;
        }
    }

    public function getPreviousAndUpdatedProductFields(object $previous, object $updated)
    {
        $previousFields = new stdClass();
        $updatedFields = new stdClass();

        foreach ($updated as $key => $value) {
            if (property_exists($previous, $key) && $previous->{$key} !== $value) {
                $updatedFields->{$key} = $value;
                $previousFields->{$key} = $previous->{$key};
            }
        }

        return [$previousFields, $updatedFields];
    }
}
