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

            $products = $this->service->getAll($adminUserId, $queryParams);

            $responseData = [
                'success' => true,
                'data' => $products
            ];

            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
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

            $responseData = [
                'success' => true,
                'data' => $product
            ];

            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function insertOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $body = $request->getParsedBody();
            $adminUserId = intval($request->getHeader('admin_user_id')[0]);

            $inserted = $this->service->insertOne($body, $adminUserId);

            if (!$inserted) {
                $responseData = [
                    'success' => false,
                    'message' => 'Falha ao inserir produto.'
                ];

                return $response->withStatus(400)->getBody()->write(json_encode($responseData));
            }

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function updateOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $body = $request->getParsedBody();
            $adminUserId = intval($request->getHeader('admin_user_id')[0]);

            $updated = $this->service->updateOne(intval($args['id']), $body, $adminUserId);

            if (!$updated) {
                $responseData = [
                    'success' => false,
                    'message' => 'Falha ao atualizar produto.'
                ];

                return $response->withStatus(400)->getBody()->write(json_encode($responseData));
            }

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $adminUserId = intval($request->getHeader('admin_user_id')[0]);

            $deleted = $this->service->deleteOne(intval($args['id']), $adminUserId);

            if (!$deleted) {
                $responseData = [
                    'success' => false,
                    'message' => 'Falha ao excluir produto.'
                ];

                return $response->withStatus(400)->getBody()->write(json_encode($responseData));
            }

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function getProductLogs(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();
            $productId = $args['id'];

            $productLogs = $this->productLogService->getLogsByProductId($productId, $queryParams);

            $responseData = [
                'success' => true,
                'data' => $productLogs
            ];

            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }
}
