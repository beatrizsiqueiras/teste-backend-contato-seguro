<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;
use Contatoseguro\TesteBackend\Enum\FilterTypes;
use Contatoseguro\TesteBackend\Enum\LogActions;
use Contatoseguro\TesteBackend\Model\AllowedFilter;
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
            $filtersQuery
            AND p.deleted_at IS NULL
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

        $updatedProduct = (object) (array_merge(array('id' => intval($id)), $body));
        $updatedProduct->active = intval($updatedProduct->active);

        [$productBefore, $produtcAfter] = $this->getPreviousAndUpdatedFields($previousProduct, $updatedProduct);


        $stm = $this->pdo->prepare("
            UPDATE product
            SET company_id = {$body['company_id']},
                title = '{$body['title']}',
                price = {$body['price']},
                active = {$body['active']}
            WHERE id = {$id}
        ");

        if (!$stm->execute()) {
            return false;
        }

        $this->productCategoryService->updateOne($id, $body['category_id']);
        $this->productLogService->insertOne($id, $adminUserId, LogActions::Update, json_encode($productBefore), json_encode($produtcAfter));
    }

    public function deleteOne($id, $adminUserId)
    {
        $this->productCategoryService->deleteOne($id);

        $stm = $this->pdo->prepare("UPDATE product SET active = 0 WHERE id = {$id}");

        if (!$stm->execute()) {
            return false;
        }

        $this->productLogService->insertOne($id, $adminUserId, LogActions::Delete);
    }

    public function getPreviousAndUpdatedFields(object $previous, object $updated)
    {

        unset($previous->created_at);
        unset($updated->category_id);

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
