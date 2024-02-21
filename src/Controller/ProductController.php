<?php

namespace ContatoSeguro\TesteBackend\Controller;

use ContatoSeguro\TesteBackend\Model\Product;
use ContatoSeguro\TesteBackend\Service\CategoryService;
use ContatoSeguro\TesteBackend\Service\ProductCategoryService;
use ContatoSeguro\TesteBackend\Service\ProductLogService;
use ContatoSeguro\TesteBackend\Service\ProductService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductController
{
    private ProductService $service;
    private CategoryService $categoryService;
    private ProductLogService $productLogService;
    private ProductCategoryService $productCategoryService;

    public function __construct()
    {
        $this->service = new ProductService();
        $this->categoryService = new CategoryService();
        $this->productCategoryService = new ProductCategoryService();
        $this->productLogService = new ProductLogService();
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {

            $adminUserId = intval($request->getHeader('admin_user_id')[0]);
            $queryParams = $request->getQueryParams();

            $stmt = $this->service->getAll($adminUserId, $queryParams);

            $response->getBody()->write(json_encode($stmt));
            return $response->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function getOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {

            $adminUserId = intval($request->getHeader('admin_user_id')[0]);

            $stmt = $this->service->getOne(intval($args['id']), $adminUserId);
            $product = Product::hydrateByFetch($stmt->fetch());

            $productCategoriesIds = $this->productCategoryService->getProductCategoriesByProductId(intval($product->id));
            $productCategoriesTitles = $this->categoryService->getCategoriesTitlesById($adminUserId, $productCategoriesIds);

            $product->setCategory($productCategoriesTitles);

            $response->getBody()->write(json_encode($product));
            return $response->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function insertOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {

            $body = $request->getParsedBody();
            $adminUserId = intval($request->getHeader('admin_user_id')[0]);

            $status = $this->service->insertOne($body, $adminUserId) ? 200 : 404;
            return $response->withStatus($status);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function updateOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {

            $body = $request->getParsedBody();
            $adminUserId = intval($request->getHeader('admin_user_id')[0]);

            $status = $this->service->updateOne(intval($args['id']), $body, $adminUserId) ? 200 : 404;
            return $response->withStatus($status);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {

            $adminUserId = intval($request->getHeader('admin_user_id')[0]);

            $status = $this->service->deleteOne(intval($args['id']), $adminUserId) ? 200 : 404;
            return $response->withStatus($status);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function getProductLogs(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {

            $queryParams = $request->getQueryParams();
            $productId = $args['id'];

            $stmt = $this->productLogService->getLogsByProductId($productId, $queryParams);

            $response->getBody()->write(json_encode($stmt));
            return $response->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }
}
