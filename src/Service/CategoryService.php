<?php

namespace ContatoSeguro\TesteBackend\Service;

use ContatoSeguro\TesteBackend\Config\DB;
use Exception;

class CategoryService
{
    private \PDO $pdo;
    private AdminUserService $adminUserService;
    private string $date;

    public function __construct()
    {
        $this->pdo = DB::connect();
        $this->adminUserService = new AdminUserService();
        $this->date =  date('Y-m-d H:i:s');
    }

    public function getAll(int $adminUserId)
    {
        $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);

        $query = "
            SELECT *
            FROM category 
            WHERE deleted_at IS NULL
            AND company_id = :companyId OR company_id IS NULL
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

    public function getOne(int $adminUserId, int $categoryId)
    {
        $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);

        $query = "
            SELECT *
            FROM category c
            WHERE c.active = 1
            AND (c.company_id = :companyId OR c.company_id IS NULL)
            AND c.id = :categoryId
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':companyId', $companyId, \PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, \PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return null;
        }
    }

    public function insertOne(array $body, int $adminUserId)
    {
        $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);
        $this->pdo->beginTransaction();

        $query = "
            INSERT INTO category (
                company_id,
                title,
                active,
                created_at
            ) VALUES (
                :companyId,
                :title,
                :active,
                :created_at
            )
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':companyId', $companyId, \PDO::PARAM_INT);
        $stmt->bindParam(':title', $body['title'], \PDO::PARAM_STR);
        $stmt->bindParam(':active', $body['active'], \PDO::PARAM_INT);
        $stmt->bindParam(':created_at', $this->date, \PDO::PARAM_STR);

        try {
            $stmt->execute();
            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            $this->pdo->rollBack();

            return false;
        }
    }

    public function updateOne(int $id, array $body, int $adminUserId)
    {
        $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);

        $this->pdo->beginTransaction();

        $query = "
            UPDATE category
            SET title = :title,
                active = :active,
                updated_at = :updatedAt
            WHERE id = :id
            AND company_id = :companyId
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':title', $body['title'], \PDO::PARAM_STR);
        $stmt->bindParam(':active', $body['active'], \PDO::PARAM_INT);
        $stmt->bindParam(':updatedAt', $this->date, \PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':companyId', $companyId, \PDO::PARAM_INT);

        try {
            $stmt->execute();
            $this->pdo->commit();

            return true;
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            $this->pdo->rollBack();

            return false;
        }
    }

    public function deleteOne(int $id, int $adminUserId)
    {
        $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);
        $this->pdo->beginTransaction();

        $query = "
            UPDATE category
                SET deleted_at = :deleted_at
            WHERE id = :id
            AND company_id = :companyId
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':deleted_at', $this->date, \PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':companyId', $companyId, \PDO::PARAM_INT);

        try {
            $this->pdo->commit();
            $stmt->execute();

            return true;
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            $this->pdo->rollBack();

            return false;
        }
    }

    public function getCategoriesTitlesById(int $adminUserId, array $categoryIds)
    {
        $categoryTitles = [];

        if (empty($categoryIds)) {
            return $categoryTitles;
        }

        foreach ($categoryIds as $category) {
            $fetchedCategory = $this->getOne($adminUserId, intval($category['id']));
            if ($fetchedCategory) {
                $categoryTitles[] = $fetchedCategory['title'];
            }
        }

        return $categoryTitles;
    }
}
