<?php

namespace ContatoSeguro\TesteBackend\Controller;

use ContatoSeguro\TesteBackend\Model\Product;
use ContatoSeguro\TesteBackend\Service\CategoryService;
use ContatoSeguro\TesteBackend\Service\ProductLogService;
use ContatoSeguro\TesteBackend\Service\ProductService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductController
{
    private ProductService $service;
    private CategoryService $categoryService;
    private ProductLogService $productLogService;

    public function __construct()
    {
        $this->service = new ProductService();
        $this->categoryService = new CategoryService();
        $this->productLogService = new ProductLogService();
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];
        $queryParams = $request->getQueryParams();

        $stm = $this->service->getAll($adminUserId, $queryParams);

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

    public function getProductLogs(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $productId = $args['id'];

        $stm = $this->productLogService->getLogsByProductId($productId, $queryParams);

        $response->getBody()->write(json_encode($stm->fetchAll()));
        return $response->withStatus(200);
    }
}
