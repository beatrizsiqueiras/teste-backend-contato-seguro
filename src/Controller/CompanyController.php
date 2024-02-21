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
        $stm = $this->service->getAll();

        $response->getBody()->write(json_encode($stm));
        return $response->withStatus(200);
    }

    public function getOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $stm = $this->service->getOne(intval($args['id']));

        $response->getBody()->write(json_encode($stm));
        return $response->withStatus(200);
    }
}
