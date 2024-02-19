<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;
use Contatoseguro\TesteBackend\Enum\LogActions;

class ProductLogService
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
            FROM product_log
        ";

        $stm = $this->pdo->prepare($query);
        $stm->execute();

        return $stm;
    }


    public function getLogsByProductId($id)
    {
        $stm = $this->pdo->prepare("
            SELECT *
            FROM product_log
            WHERE product_id = {$id}
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
}
