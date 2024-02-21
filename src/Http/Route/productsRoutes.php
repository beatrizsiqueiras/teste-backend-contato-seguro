<?php

use ContatoSeguro\TesteBackend\Http\Controller\ProductController;
use Slim\Routing\RouteCollectorProxy;

$app->group('/products', function (RouteCollectorProxy $group) {
    $group->get('', [ProductController::class, 'getAll']);
    $group->get('/{id}', [ProductController::class, 'getOne']);
    $group->get('/{id}/logs', [ProductController::class, 'getProductLogs']);
    $group->post('', [ProductController::class, 'insertOne']);
    $group->put('/{id}', [ProductController::class, 'updateOne']);
    $group->delete('/{id}', [ProductController::class, 'deleteOne']);
});
