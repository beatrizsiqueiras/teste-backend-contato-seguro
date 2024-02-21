<?php

namespace ContatoSeguro\TesteBackend\Service;

use ContatoSeguro\TesteBackend\Config\DB;
use PDO;
use PDOStatement;

class CompanyService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function getAll(): array
    {
        try {
            $query = "
            SELECT 
                *
            FROM 
                company 
            WHERE
                deleted_at IS NULL
            ";

            $stmt = $this->pdo->prepare($query);
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
                company 
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

    public function getNameById(int $id): string
    {
        try {
            $query = "
            SELECT
                name
            FROM
                company
            WHERE
                id = :id
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? $result['name'] : '';
        } catch (\PDOException $e) {
            error_log('Erro ao executar a consulta SQL: ' . $e->getMessage());
            
            return null;
        }
    }
}
