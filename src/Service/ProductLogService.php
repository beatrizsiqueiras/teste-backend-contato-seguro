<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;

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


    public function getLogByProductId($id)
    {
        $stm = $this->pdo->prepare("
            SELECT *
            FROM product_log
            WHERE product_id = {$id}
        ");

        $stm->execute();

        return $stm;
    }


}
