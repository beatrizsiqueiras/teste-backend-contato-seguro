<?php

use ContatoSeguro\TesteBackend\Http\Controller\AdminUserController;
use Slim\Routing\RouteCollectorProxy;

$app->group('/adminUsers', function (RouteCollectorProxy $group) {
    $group->get('', [AdminUserController::class, 'getAll']);
    $group->get('/{id}', [AdminUserController::class, 'getOne']);
    $group->post('', [AdminUserController::class, 'insertOne']);
    $group->put('/{id}', [AdminUserController::class, 'updateOne']);
    $group->delete('/{id}', [AdminUserController::class, 'deleteOne']);
});
