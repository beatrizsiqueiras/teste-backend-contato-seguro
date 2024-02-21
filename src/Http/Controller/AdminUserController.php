<?php

namespace ContatoSeguro\TesteBackend\Http\Controller;

use ContatoSeguro\TesteBackend\Model\AdminUser;
use ContatoSeguro\TesteBackend\Service\AdminUserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdminUserController
{
    private AdminUserService $service;

    public function __construct()
    {
        $this->service = new AdminUserService();
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $companyId = $request->getHeader('company_id')[0];
            $users = $this->service->getAll(intval($companyId));

            $responseData = [
                'success' => true,
                'data' => $users
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
            $stmt = $this->service->getOne(intval($args['id']));
            $adminUser = AdminUser::hydrateByFetch($stmt->fetch());

            $responseData = [
                'success' => true,
                'data' => $adminUser
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
            $inserted = $this->service->insertOne($body);

            if (!$inserted) {
                $responseData = [
                    'success' => false,
                    'message' => 'Falha ao inserir usuário.'
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
            $updated = $this->service->updateOne(intval($args['id']), $body);

            if (!$updated) {
                $responseData = [
                    'success' => false,
                    'message' => 'Falha ao atualizar usuário.'
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
            $deleted = $this->service->deleteOne(intval($args['id']));

            if (!$deleted) {
                $responseData = [
                    'success' => false,
                    'message' => 'Falha ao inserir usuário.'
                ];

                return $response->withStatus(400)->getBody()->write(json_encode($responseData));
            }

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }
}
