<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Model\Product;
use Contatoseguro\TesteBackend\Service\CategoryService;
use Contatoseguro\TesteBackend\Service\ProductService;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductController
{
    private ProductService $service;
    private CategoryService $categoryService;

    public function __construct()
    {
        $this->service = new ProductService();
        $this->categoryService = new CategoryService();
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        $stm = $this->service->getAll($adminUserId);
        $response->getBody()->write(json_encode($stm->fetchAll()));
        return $response->withStatus(200);
    }

    public function getOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $stm = $this->service->getOne($args['id']);

        $product = Product::hydrateByFetch($stm->fetch());

        $adminUserId = $request->getHeader('admin_user_id')[0];

        $productCategory = $this->categoryService->getProductCategory($product->id)->fetchAll();

        $productCategoryTitles = $this->categoryService->getAllProductCategoryTitles($adminUserId, $productCategory);

        $product->setCategory($productCategoryTitles);

        $response->getBody()->write(json_encode($product));
        return $response->withStatus(200);
    }

    public function insertOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $adminUserId = $request->getHeader('admin_user_id')[0];

        if ($this->service->insertOne($body, $adminUserId)) {
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }

    public function updateOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $adminUserId = $request->getHeader('admin_user_id')[0];

        if ($this->service->updateOne($args['id'], $body, $adminUserId)) {
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }

    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        if ($this->service->deleteOne($args['id'], $adminUserId)) {
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }

    public function getActiveProduct(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // $queryParams = $request->getQueryParams();        
        // $isActive = isset($queryParams['active']) ? (bool)$queryParams['active'] : null;

        $adminUserId = $request->getHeader('admin_user_id')[0];

        $activeProducts = $this->service->getAllActiveProducts($adminUserId);

        $response->getBody()->write(json_encode($activeProducts->fetchAll()));

        return $response->withStatus(200);
    }

    public function getInactiveProduct(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        $inactiveProducts = $this->service->getAllInactiveProducts($adminUserId);

        $response->getBody()->write(json_encode($inactiveProducts->fetchAll()));

        return $response->withStatus(200);
    }

    public function getProductByCategory(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $categoryId = $args['categoryId'];

        $adminUserId = $request->getHeader('admin_user_id')[0];

        $productsByCategoryId = $this->service->getProductByCategoryId($adminUserId, $categoryId);

        $response->getBody()->write(json_encode($productsByCategoryId->fetchAll()));

        return $response->withStatus(200);
    }

    public function getProductByCreatedDate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $date = $args['date'];
        $adminUserId = $request->getHeader('admin_user_id')[0];

        $productsByCreatedDate = $this->service->getProductByCreatedDate($adminUserId, $date);

        $response->getBody()->write(json_encode($productsByCreatedDate->fetchAll()));

        return $response->withStatus(200);
    }
}
