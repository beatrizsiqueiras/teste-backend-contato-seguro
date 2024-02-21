<?php

use ContatoSeguro\TesteBackend\Http\Controller\CategoryController;
use Slim\Routing\RouteCollectorProxy;

$app->group('/categories', function (RouteCollectorProxy $group) {
    $group->get('', [CategoryController::class, 'getAll']);
    $group->get('/{id}', [CategoryController::class, 'getOne']);
    $group->post('', [CategoryController::class, 'insertOne']);
    $group->put('/{id}', [CategoryController::class, 'updateOne']);
    $group->delete('/{id}', [CategoryController::class, 'deleteOne']);
});
