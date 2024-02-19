<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Service\ProductCategoryService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductCategoryController
{
    private ProductCategoryService $service;

    public function __construct()
    {
        $this->service = new ProductCategoryService();
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $companyId = $request->getHeader('company_id')[0];

        $stm = $this->service->getAll($companyId);

        $response->getBody()->write(json_encode($stm->fetchAll()));
        return $response->withStatus(200);
    }

    public function getProductCategoryById(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $companyId = $request->getHeader('company_id')[0];
        $stm = $this->service->getProductCategoryById($args['id'], $companyId);

        $response->getBody()->write(json_encode($stm->fetchAll()));
        return $response->withStatus(200);
    }
}
