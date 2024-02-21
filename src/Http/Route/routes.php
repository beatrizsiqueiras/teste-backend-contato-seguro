<?php

use ContatoSeguro\TesteBackend\Http\Controller\CompanyController;
use ContatoSeguro\TesteBackend\Http\Controller\HomeController;
use ContatoSeguro\TesteBackend\Http\Controller\ProductLogController;
use ContatoSeguro\TesteBackend\Http\Controller\ReportController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/** @var App $app*/
$app->get('/', [HomeController::class, 'home']);

$app->group('/companies', function (RouteCollectorProxy $group) {
    $group->get('', [CompanyController::class, 'getAll']);
    $group->get('/{id}', [CompanyController::class, 'getOne']);
});

$app->get('/report', [ReportController::class, 'generateReport']);

$app->group('/productLogs', function (RouteCollectorProxy $group) {
    $group->get('', [ProductLogController::class, 'getAll']);
});

require 'productsRoutes.php';

require 'categoriesRoutes.php';

require 'adminUsersRoutes.php';
