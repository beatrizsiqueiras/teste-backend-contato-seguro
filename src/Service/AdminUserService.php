<?php

namespace ContatoSeguro\TesteBackend\Service;

use ContatoSeguro\TesteBackend\Config\DB;
use Exception;
use PDO;
use PDOStatement;

class AdminUserService
{
    private \PDO $pdo;
    private string $date;

    public function __construct()
    {
        $this->pdo = DB::connect();
        $this->date =  date('Y-m-d H:i:s');
    }

    public function getAll(int $companyId): array
    {
        try {

            $query = "
            SELECT
                *
            FROM
                admin_user
            WHERE
                company_id = :companyId
                AND deleted_at IS NULL
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':companyId', $companyId, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return [];
        }
    }

    public function getOne(int $id): PDOStatement
    {
        try {
            $query = "
            SELECT
                *
            FROM
                admin_user
            WHERE
                id = :id
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt;
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return null;
        }
    }

    public function insertOne(array $body): bool
    {
        try {

            $query = "
            INSERT
                INTO
                admin_user (
                company_id,
                email,
                name,
                created_at
            )
            VALUES (
                :companyId,
                :email,
                :name,
                :createdAt
            )
            ";
            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(':companyId', $body['company_id'], \PDO::PARAM_INT);
            $stmt->bindParam(':email', $body['email'], \PDO::PARAM_STR);
            $stmt->bindParam(':name', $body['name'], \PDO::PARAM_STR);
            $stmt->bindParam(':createdAt', $this->date, \PDO::PARAM_STR);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return false;
        }
    }

    public function updateOne(int $id, array $body): bool
    {
        try {
            $query = "
            UPDATE
                admin_user
            SET
                company_id = :company_id,
                email = :email,
                name = :name,
                updated_at = :updated_at
            WHERE
                id = :id
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':company_id', $body['company_id'], \PDO::PARAM_INT);
            $stmt->bindParam(':email', $body['email'], \PDO::PARAM_STR);
            $stmt->bindParam(':name', $body['name'], \PDO::PARAM_STR);
            $stmt->bindParam(':updated_at', $this->date, \PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteOne(int $id): bool
    {
        try {
            $query = "
            UPDATE
                admin_user
            SET
                deleted_at = :deleted_at
            WHERE
                id = :id
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':deleted_at', $this->date, \PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return false;
        }
    }

    public function getCompanyIdFromAdminUser(int $adminUserId): int
    {
        try {
            $query = "
            SELECT
                company_id
            FROM
                admin_user
            WHERE
                id = :adminUserId
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':adminUserId', $adminUserId, \PDO::PARAM_INT);

            $stmt->execute();
            $company = $stmt->fetch(\PDO::FETCH_ASSOC);

            return  intval($company['company_id']);
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            return 0;
        }
    }
}
