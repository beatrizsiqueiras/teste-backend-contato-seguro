<?php

namespace ContatoSeguro\TesteBackend\Controller;

use ContatoSeguro\TesteBackend\Service\CompanyService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CompanyController
{
    private CompanyService $service;

    public function __construct()
    {
        $this->service = new CompanyService();
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $companies = $this->service->getAll();

            $responseData = [
                'success' => true,
                'data' => $companies
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

            $company = $this->service->getOne(intval($args['id']));

            $responseData = [
                'success' => true,
                'data' => $company
            ];

            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(500)->getBody()->write(json_encode(['error' => $e->getMessage()]));
        }
    }
}
