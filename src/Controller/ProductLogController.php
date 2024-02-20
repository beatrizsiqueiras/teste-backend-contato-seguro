<?php

namespace ContatoSeguro\TesteBackend\Controller;

use ContatoSeguro\TesteBackend\Model\Product;
use ContatoSeguro\TesteBackend\Service\CategoryService;
use ContatoSeguro\TesteBackend\Service\ProductLogService;
use ContatoSeguro\TesteBackend\Service\ProductService;
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
        $queryParams = $request->getQueryParams();

        $stm = $this->service->getAll($queryParams);

        $response->getBody()->write(json_encode($stm->fetchAll()));
        return $response->withStatus(200);
    }
}
