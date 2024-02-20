<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;
use Exception;

class AdminUserService
{
    private \PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function getAll($companyId)
    {
        $query = "
            SELECT *
            FROM admin_user
            WHERE company_id = {$companyId}
            AND deleted_at IS NULL
        ";

        $stm = $this->pdo->prepare($query);

        $stm->execute();

        return $stm;
    }

    public function getOne($id)
    {
        $stm = $this->pdo->prepare("
            SELECT *
            FROM admin_user
            WHERE id = {$id}
        ");

        $stm->execute();

        return $stm;
    }

    public function insertOne($body)
    {
        $stm = $this->pdo->prepare("
            INSERT INTO admin_user (
                company_id,
                email,
                name
            ) VALUES (
                {$body['company_id']},
                '{$body['email']}',
                {$body['name']}
            )
        ");

        return $stm->execute();
    }

    public function updateOne($id, $body)
    {
        $stm = $this->pdo->prepare("
            UPDATE admin_user
            SET company_id = {$body['company_id']},
                email = '{$body['email']}',
                name = {$body['name']}
            WHERE id = {$id}
        ");

        return $stm->execute();
    }

    public function deleteOne($id)
    {
        $stm = $this->pdo->prepare("
            DELETE FROM admin_user WHERE id = {$id}
        ");
        return $stm->execute();
    }

    public function getCompanyFromAdminUser($adminUserId)
    {
        $query = "
            SELECT company_id
            FROM admin_user
            WHERE id = {$adminUserId}
        ";

        $stm = $this->pdo->prepare($query);
        $stm->execute();

        return $stm->fetch()->company_id;
    }
}
