<?php

namespace ContatoSeguro\TesteBackend\Service;

use ContatoSeguro\TesteBackend\Config\DB;

class ProductCategoryService
{
    private \PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function getAll($company_id)
    {
        $query = "
            SELECT pc.*
            FROM product_category pc
            INNER JOIN product p ON p.id = pc.product_id
            AND p.company_id = {$company_id}
            AND pc.deleted_at IS NULL
        ";

        $stm = $this->pdo->prepare($query);
        $stm->execute();

        return $stm;
    }

    public function getProductCategoriesByProductId(int $productId)
    {
        $query = "
            SELECT c.id
            FROM category c
            INNER JOIN product_category pc
                ON pc.category_id = c.id
            WHERE pc.product_id = :productId
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':productId', $productId, \PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return $stmt;
        }
    }

    public function insertOne($productId, $categoryId)
    {
        $query = "INSERT INTO product_category (
            product_id,
            category_id
        ) VALUES (
            {$productId},
            {$categoryId}
        )";

        $stm = $this->pdo->prepare($query);

        return $stm->execute();
    }

    public function updateOne($productId, $categoryId)
    {
        $query = "UPDATE product_category SET category_id = {$categoryId} WHERE product_id = {$productId}";

        $stm = $this->pdo->prepare($query);

        return $stm->execute();
    }

    public function deleteOne($productId)
    {
        $query = "DELETE FROM product_category WHERE product_id = {$productId}";

        $stm = $this->pdo->prepare($query);

        return $stm->execute();
    }
}
