<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;

class ProductCategoryService
{
    private \PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function getAll()
    {
        $query = "
            SELECT *
            FROM product_category
        ";

        $stm = $this->pdo->prepare($query);
        $stm->execute();

        return $stm;
    }

    public function getProductCategoryByProductId($id)
    {
        $stm = $this->pdo->prepare("
            SELECT *
            FROM product_category
            WHERE product_id = {$id}
        ");

        $stm->execute();

        return $stm;
    }

    public function insertOne($productId, $categoryId)
    {
        $query = "INSERT INTO product_category (
            product_id,
            cat_id
        ) VALUES (
            {$productId},
            {$categoryId}
        )";

        $stm = $this->pdo->prepare($query);

        return $stm->execute();
    }

    public function updateOne($productId, $categoryId)
    {
        $query = "UPDATE product_category SET cat_id = {$categoryId} WHERE product_id = {$productId}";

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
