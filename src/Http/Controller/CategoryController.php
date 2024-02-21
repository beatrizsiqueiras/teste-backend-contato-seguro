<?php

namespace ContatoSeguro\TesteBackend\Http\Controller;

use ContatoSeguro\TesteBackend\Model\Category;
use ContatoSeguro\TesteBackend\Service\CategoryService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CategoryController
{
    private CategoryService $service;

    public function __construct()
    {
        $this->service = new CategoryService();
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $adminUserId = intval($request->getHeader('admin_user_id')[0]);

            $categories = $this->service->getAll($adminUserId);

            $responseData = [
                'success' => true,
                'data' => $categories
            ];

            $response->getBody()->write(json_encode($responseData));

            return $response->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function getOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $adminUserId = intval($request->getHeader('admin_user_id')[0]);

            $stmt = $this->service->getOne($adminUserId, $args['id']);
            $category = Category::hydrateByFetch($stmt->fetch());

            $responseData = [
                'success' => true,
                'data' => $category
            ];

            $response->getBody()->write(json_encode($responseData));

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

            $inserted = $this->service->insertOne($body, $adminUserId);

            if (!$inserted) {
                $responseData = [
                    'success' => false,
                    'message' => 'Falha ao inserir categoria.'
                ];

                return $response->withStatus(400)->getBody()->write(json_encode($responseData));
            }

            return $response->withStatus(200);
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
                    'message' => 'Falha ao atualizar categoria.'
                ];

                return $response->withStatus(400)->getBody()->write(json_encode($responseData));
            }

            return $response->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $adminUserId = intval(($request->getHeader('admin_user_id')[0]));

            $deleted = $this->service->deleteOne(intval($args['id']), $adminUserId);

            if (!$deleted) {
                $responseData = [
                    'success' => false,
                    'message' => 'Falha ao excluir categoria.'
                ];

                return $response->withStatus(400)->getBody()->write(json_encode($responseData));
            }

            return $response->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }
}
