<?php

namespace ContatoSeguro\TesteBackend\Controller;

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
        $adminUserId = intval($request->getHeader('admin_user_id')[0]);

        $stm = $this->service->getAll($adminUserId);

        $response->getBody()->write(json_encode($stm));
        return $response->withStatus(200);
    }

    public function getOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = intval($request->getHeader('admin_user_id')[0]);

        $stm = $this->service->getOne($adminUserId, $args['id']);

        $response->getBody()->write(json_encode($stm));
        return $response->withStatus(200);
    }

    public function insertOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $adminUserId = intval($request->getHeader('admin_user_id')[0]);

        $status = $this->service->insertOne($body, $adminUserId) ? 200 : 404;

        return $response->withStatus($status);
    }

    public function updateOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $adminUserId = intval($request->getHeader('admin_user_id')[0]);

        $status = $this->service->updateOne(intval($args['id']), $body, $adminUserId) ? 200 : 404;
        return $response->withStatus($status);
    }

    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = intval(($request->getHeader('admin_user_id')[0]));

        $status = $this->service->deleteOne(intval($args['id']), $adminUserId) ? 200 : 404;
        return $response->withStatus($status);
    }
}
