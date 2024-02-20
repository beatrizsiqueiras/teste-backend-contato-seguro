<?php

namespace ContatoSeguro\TesteBackend\Controller;

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
        $companyId = $request->getHeader('company_id')[0];

        $stm = $this->service->getAll(intval($companyId));
        $response->getBody()->write(json_encode($stm));
        return $response->withStatus(200);
    }

    public function getOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $stm = $this->service->getOne(intval($args['id']));

        $response->getBody()->write(json_encode($stm));
        return $response->withStatus(200);
    }

    public function insertOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();

        $status = $this->service->insertOne($body) ? 200 : 404;
        return $response->withStatus($status);
    }

    public function updateOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();

        $status =  $this->service->updateOne(intval($args['id']), $body) ? 200 : 404;
        return $response->withStatus($status);
    }

    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $status =  $this->service->deleteOne(intval($args['id'])) ? 200 : 404;
        return $response->withStatus($status);
    }
}
