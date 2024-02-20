<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Model\Product;
use Contatoseguro\TesteBackend\Service\CategoryService;
use Contatoseguro\TesteBackend\Service\ProductLogService;
use Contatoseguro\TesteBackend\Service\ProductService;
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
