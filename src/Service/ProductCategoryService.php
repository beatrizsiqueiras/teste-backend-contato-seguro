<?php

namespace ContatoSeguro\TesteBackend\Service;

use ContatoSeguro\TesteBackend\Config\DB;

class ProductCategoryService
{
    private \PDO $pdo;
    private string $now;

    public function __construct()
    {
        $this->pdo = DB::connect();
        $this->now =  now();
    }

    public function getAll(int $companyId): array
    {
        try {
            $query = "
            SELECT 
                pc.*
            FROM 
                product_category pc
            INNER JOIN product p 
                ON p.id = pc.product_id
            WHERE p.company_id = :companyId
            AND pc.deleted_at IS NULL
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":companyId", $companyId, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());

            return [];
        }
    }

    public function getProductCategoriesByProductId(int $productId): array
    {
        try {
            $query = "
            SELECT 
                c.id
            FROM
                category c
            INNER JOIN product_category pc
                ON pc.category_id = c.id
            WHERE pc.product_id = :productId
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':productId', $productId, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return [];
        }
    }

    public function insertOne(int $productId, int $categoryId): bool
    {
        try {
            $query = "
            INSERT INTO product_category (
                product_id,
                category_id,
                created_at
            ) 
            VALUES (
                :productId,
                :categoryId,
                :createdAt
            )";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':productId', $productId, \PDO::PARAM_INT);
            $stmt->bindParam(':categoryId', $categoryId, \PDO::PARAM_INT);
            $stmt->bindParam(':createdAt', $this->now, \PDO::PARAM_STR);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return false;
        }
    }

    public function updateOne($productId, $categoryId): bool
    {
        try {
            $query = "
            UPDATE 
                product_category 
            SET 
                category_id = :categoryId,
                updated_at = :updatedAt
            WHERE product_id = :productId
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':categoryId', $categoryId, \PDO::PARAM_INT);
            $stmt->bindParam(':updatedAt', $this->now, \PDO::PARAM_STR);
            $stmt->bindParam(':productId', $productId, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());

            return false;
        }
    }

    public function deleteOne(int $productId): bool
    {
        try {
            $query = "
            UPDATE
                product_category
            SET
                deleted_at = :deletedAt
            WHERE
                product_id = :productId
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':deletedAt', $this->now, \PDO::PARAM_STR);
            $stmt->bindParam(':productId', $productId, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());

            return false;
        }
    }
}
