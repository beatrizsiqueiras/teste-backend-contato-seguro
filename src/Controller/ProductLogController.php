<?php

namespace ContatoSeguro\TesteBackend\Controller;

use ContatoSeguro\TesteBackend\Service\ProductLogService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductLogController
{
    private ProductLogService $service;

    public function __construct()
    {
        $this->service = new ProductLogService();
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $queryParams = $request->getQueryParams();
            $logs = $this->service->getAll($queryParams);

            $responseData = [
                'success' => true,
                'data' => $logs
            ];

            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }
}
