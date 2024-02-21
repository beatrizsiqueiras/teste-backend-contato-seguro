<?php

use ContatoSeguro\TesteBackend\Controller\AdminUserController;
use ContatoSeguro\TesteBackend\Controller\CategoryController;
use ContatoSeguro\TesteBackend\Controller\CompanyController;
use ContatoSeguro\TesteBackend\Controller\HomeController;
use ContatoSeguro\TesteBackend\Controller\ProductController;
use ContatoSeguro\TesteBackend\Controller\ProductLogController;
use ContatoSeguro\TesteBackend\Controller\ReportController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/** @var App $app*/
$app->get('/', [HomeController::class, 'home']);

$app->group('/companies', function (RouteCollectorProxy $group) {
    $group->get('', [CompanyController::class, 'getAll']);
    $group->get('/{id}', [CompanyController::class, 'getOne']);
});

$app->group('/products', function (RouteCollectorProxy $group) {
    $group->get('', [ProductController::class, 'getAll']);
    $group->get('/{id}', [ProductController::class, 'getOne']);
    $group->get('/{id}/logs', [ProductController::class, 'getProductLogs']);
    $group->post('', [ProductController::class, 'insertOne']);
    $group->put('/{id}', [ProductController::class, 'updateOne']);
    $group->delete('/{id}', [ProductController::class, 'deleteOne']);
});

$app->group('/categories', function (RouteCollectorProxy $group) {
    $group->get('', [CategoryController::class, 'getAll']);
    $group->get('/{id}', [CategoryController::class, 'getOne']);
    $group->post('', [CategoryController::class, 'insertOne']);
    $group->put('/{id}', [CategoryController::class, 'updateOne']);
    $group->delete('/{id}', [CategoryController::class, 'deleteOne']);
});

$app->group('/adminUsers', function (RouteCollectorProxy $group) {
    $group->get('', [AdminUserController::class, 'getAll']);
    $group->get('/{id}', [AdminUserController::class, 'getOne']);
    $group->post('', [AdminUserController::class, 'insertOne']);
    $group->put('/{id}', [AdminUserController::class, 'updateOne']);
    $group->delete('/{id}', [AdminUserController::class, 'deleteOne']);
});

$app->get('/report', [ReportController::class, 'generateReport']);

$app->group('/productLogs', function (RouteCollectorProxy $group) {
    $group->get('', [ProductLogController::class, 'getAll']);
});