<?php

namespace ContatoSeguro\TesteBackend\Service;

use ContatoSeguro\TesteBackend\Config\DB;
use ContatoSeguro\TesteBackend\Enum\FilterTypes;
use ContatoSeguro\TesteBackend\Enum\LogActions;
use ContatoSeguro\TesteBackend\Model\AllowedFilter;
use stdClass;

class ProductService
{
    private \PDO $pdo;
    private ProductLogService $productLogService;
    private ProductCategoryService $productCategoryService;

    public function __construct()
    {
        $this->pdo = DB::connect();
        $this->productLogService = new ProductLogService();
        $this->productCategoryService = new ProductCategoryService();
    }

    public function getAll(string $adminUserId, array $queryParams = [])
    {
        $filtersQuery = get_filters_query($queryParams, [
            'createdAt' => new AllowedFilter('p.created_at', FilterTypes::Date),
            'active' => new AllowedFilter('p.active')
        ]);

        $query = "
            SELECT p.*, c.title as category
            FROM product p
            INNER JOIN product_category pc ON pc.product_id = p.id
            INNER JOIN category c ON c.id = pc.cat_id
            WHERE p.company_id = {$adminUserId}
            AND p.deleted_at IS NULL
            $filtersQuery
        ";

        $stm = $this->pdo->prepare($query);

        $stm->execute();

        return $stm;
    }

    public function getOne($id)
    {
        $stm = $this->pdo->prepare("
            SELECT *
            FROM product
            WHERE id = {$id}
        ");

        $stm->execute();

        return $stm;
    }

    public function insertOne($body, $adminUserId)
    {
        $stm = $this->pdo->prepare("
            INSERT INTO product (
                company_id,
                title,
                price,
                active
            ) VALUES (
                $body[company_id],
                '$body[title]',
                $body[price],
                $body[active]
            )
        ");
        $stm->execute();

        $productId = $this->pdo->lastInsertId();

        $this->productCategoryService->insertOne($productId, $body['category_id']);
        $this->productLogService->insertOne($productId, $adminUserId, LogActions::Create);
    }

    public function updateOne($id, $body, $adminUserId)
    {
        $previousProduct = $this->getOne($id)->fetch();
        $updatedAt = date('Y-m-d H:i:s');

        $stm = $this->pdo->prepare("
            UPDATE product
            SET company_id = {$body['company_id']},
                title = '{$body['title']}',
                price = {$body['price']},
                active = {$body['active']},
                updated_at = '{$updatedAt}'
            WHERE id = {$id}
        ");

        if (!$stm->execute()) {
            return false;
        }

        $this->productCategoryService->updateOne($id, $body['category_id']);

        $updatedProduct = $this->getOne($id)->fetch();;

        [$productBefore, $produtcAfter] = $this->getPreviousAndUpdatedProductFields($previousProduct, $updatedProduct);

        $this->productLogService->insertOne($id, $adminUserId, LogActions::Update, json_encode($productBefore), json_encode($produtcAfter));
    }

    public function deleteOne($id, $adminUserId)
    {
        $this->productCategoryService->deleteOne($id);
        $deletedAt = date('Y-m-d H:i:s');

        $stm = $this->pdo->prepare("UPDATE product SET deleted_at = '{$deletedAt}' WHERE id = {$id}");

        if (!$stm->execute()) {
            return false;
        }

        $this->productLogService->insertOne($id, $adminUserId, LogActions::Delete);
    }

    public function getPreviousAndUpdatedProductFields(object $previous, object $updated)
    {
        $previousFields = new stdClass();
        $updatedFields = new stdClass();

        foreach ($updated as $key => $value) {
            if (property_exists($previous, $key) && $previous->{$key} !== $value) {
                $updatedFields->{$key} = $value;
                $previousFields->{$key} = $previous->{$key};
            }
        }

        return [$previousFields, $updatedFields];
    }
}
