<?php

namespace ContatoSeguro\TesteBackend\Service;

use ContatoSeguro\TesteBackend\Config\DB;
use Exception;

class CategoryService
{
    private \PDO $pdo;
    private AdminUserService $adminUserService;

    public function __construct()
    {
        $this->pdo = DB::connect();
        $this->adminUserService = new AdminUserService();
    }

    public function getAll($adminUserId)
    {
        $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);
        
        $query = "
            SELECT *
            FROM category 
            WHERE company_id = $companyId OR company_id IS NULL
            AND deleted_at IS NULL;
        ";

        $stm = $this->pdo->prepare($query);
        $stm->execute();

        return $stm;
    }

    public function getOne($adminUserId, $categoryId)
    {
        $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);

        $query = "
            SELECT *
            FROM category c
            WHERE c.active = 1
            AND (c.company_id = {$companyId} OR c.company_id IS NULL)
            AND c.id = {$categoryId}
        ";

        $stm = $this->pdo->prepare($query);
        $stm->execute();

        return $stm;
    }

    public function getProductCategory($productId)
    {
        $query = "
            SELECT c.id
            FROM category c
            INNER JOIN product_category pc
                ON pc.cat_id = c.id
            WHERE pc.product_id = {$productId}
        ";

        $stm = $this->pdo->prepare($query);
        $stm->execute();

        return $stm;
    }

    public function insertOne($body, $adminUserId)
    {
        $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);

        $stm = $this->pdo->prepare("
            INSERT INTO category (
                company_id,
                title,
                active
            ) VALUES (
                {$companyId},
                '{$body['title']}',
                {$body['active']}
            )
        ");

        return $stm->execute();
    }

    public function updateOne($id, $body, $adminUserId)
    {
        $active = (int)$body['active'];
        $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);

        $stm = $this->pdo->prepare("
            UPDATE category
            SET title = '{$body['title']}',
                active = {$active}
            WHERE id = {$id}
            AND company_id = {$companyId}
        ");

        return $stm->execute();
    }

    public function deleteOne($id, $adminUserId)
    {
        $companyId = $this->adminUserService->getCompanyIdFromAdminUser($adminUserId);

        $stm = $this->pdo->prepare("
            DELETE
            FROM category
            WHERE id = {$id}
            AND company_id = {$companyId}
        ");

        return $stm->execute();
    }

    public function getAllProductCategoryTitles(int $adminUserId, array $categoryIds)
    {
        if (empty($categoryIds)) {
            throw new Exception("Empty array");
        }

        $categoryTitles = array();

        foreach ($categoryIds as $category) {
            $fetchedCategory = $this->getOne($adminUserId, $category->id)->fetch();

            $categoryTitles[] = $fetchedCategory->title;
        }

        return $categoryTitles;
    }
}
