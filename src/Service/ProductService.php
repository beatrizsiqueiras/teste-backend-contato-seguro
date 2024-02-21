<?php

namespace ContatoSeguro\TesteBackend\Service;

use ContatoSeguro\TesteBackend\Config\DB;
use ContatoSeguro\TesteBackend\Enum\FilterTypes;
use ContatoSeguro\TesteBackend\Enum\LogActions;
use ContatoSeguro\TesteBackend\Model\AllowedFilter;
use PDOStatement;
use stdClass;

class ProductService
{
    private \PDO $pdo;
    private ProductLogService $productLogService;
    private ProductCategoryService $productCategoryService;
    private AdminUserService $adminUserService;
    private string $date;

    public function __construct()
    {
        $this->pdo = DB::connect();
        $this->productLogService = new ProductLogService();
        $this->productCategoryService = new ProductCategoryService();
        $this->adminUserService = new AdminUserService();
        $this->date =  date('Y-m-d H:i:s');
    }

    public function getAll(int $adminUserId, array $queryParams = []): array
    {
        try {
            $filtersQuery = get_filters_query($queryParams, [
                'categoryId' => new AllowedFilter('c.id'),
                'active' => new AllowedFilter('p.active'),
            ]);

            // $sortingQuery = get_sorting_query($queryParams, [
            //     'orderByCreatedAt' => new AllowedFilter('p.created_at', FilterTypes::OrderBy)
            // ]);

            $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);

            $query = "
            SELECT
                p.*,
                c.title AS category
            FROM
                product p
            INNER JOIN product_category pc ON
                pc.product_id = p.id
            INNER JOIN category c ON
                c.id = pc.category_id
            WHERE
                p.deleted_at IS NULL
                AND p.company_id = :companyId
                $filtersQuery
            ";
            var_dump($query);
            exit;
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':companyId', $companyId, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return [];
        }
    }

    public function getOne(int $id, int $adminUserId): PDOStatement
    {
        try {

            $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);

            $query = "
            SELECT
                *
            FROM
                product
            WHERE
                id = :id
                AND company_id = :companyId
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->bindParam(':companyId', $companyId, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt;
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return null;
        }
    }

    public function insertOne(array $data, string $adminUserId): bool
    {
        try {
            $query = "
            INSERT INTO product (
                company_id,
                title,
                price,
                active
            ) 
            VALUES (
                :companyId,
                :title,
                :price,
                :active
            )
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':companyId', $data['company_id'], \PDO::PARAM_INT);
            $stmt->bindParam(':title', $data['title'], \PDO::PARAM_STR);
            $stmt->bindParam(':price', $data['price'], \PDO::PARAM_INT);
            $stmt->bindParam(':active', $data['active'], \PDO::PARAM_INT);
            $stmt->execute();

            $productId = $this->pdo->lastInsertId();

            $this->productCategoryService->insertOne($productId, $data['category_id']);
            $this->productLogService->insertOne(intval($productId), intval($adminUserId), LogActions::Create);

            return true;
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return false;
        }
    }

    public function updateOne(int $id, array $data, string $adminUserId): bool
    {
        try {
            $previousProduct = $this->getOne($id, $adminUserId)->fetch(\PDO::FETCH_ASSOC);

            if (!$previousProduct) {
                return false;
            }

            $query = "
            UPDATE
                product
            SET
                title = :title,
                active = :active,
                updated_at = :updatedAt,
                price = :price
            WHERE
                id = :id
                AND company_id = :companyId";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':title', $data['title'], \PDO::PARAM_STR);
            $stmt->bindParam(':active', $data['active'], \PDO::PARAM_INT);
            $stmt->bindParam(':price', $data['price'], \PDO::PARAM_STR);
            $stmt->bindParam(':updatedAt', $this->date, \PDO::PARAM_STR);
            $stmt->bindParam(':companyId', $data['company_id'], \PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

            if (!$stmt->execute()) {
                return false;
            }

            $this->productCategoryService->updateOne($id, $data['category_id']);

            $updatedProduct = $this->getOne($id, $adminUserId)->fetch(\PDO::FETCH_ASSOC);

            if (!$updatedProduct) {
                return false;
            }

            [$previousFields, $updatedFields] = $this->getPreviousAndUpdatedProductFields((object)$previousProduct, (object)$updatedProduct);

            $this->productLogService->insertOne(
                $id,
                $adminUserId,
                LogActions::Update,
                json_encode($previousFields),
                json_encode($updatedFields)
            );

            return true;
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteOne(int $id, string $adminUserId): bool
    {
        try {
            $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);

            $query = "
            UPDATE
                product
            SET
                deleted_at = :deletedAt
            WHERE
                id = :id
                AND company_id = :companyId
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':deletedAt', $this->date, \PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->bindParam(':companyId', $companyId, \PDO::PARAM_INT);

            if (!$stmt->execute()) {
                return false;
            }

            $this->productCategoryService->deleteOne($id);
            $this->productLogService->insertOne($id, $adminUserId, LogActions::Delete);

            return true;
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());

            return false;
        }
    }

    public function getPreviousAndUpdatedProductFields(object $previous, object $updated): array
    {
        $previousFields = new stdClass();
        $updatedFields = new stdClass();

        foreach ($previous as $key => $value) {
            if (property_exists($updated, $key)) {
                if ($updated->{$key} !== $value) {
                    $updatedFields->{$key} = $value;
                    $previousFields->{$key} = $updated->{$key};
                }
            }
        }

        return [$previousFields, $updatedFields];
    }
}
