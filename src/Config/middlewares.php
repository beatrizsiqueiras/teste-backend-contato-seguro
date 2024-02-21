<?php

use ContatoSeguro\TesteBackend\Http\Middleware\JsonResponseMiddleware;
use Slim\App;

/** @var App $app*/
$app->addBodyParsingMiddleware();
$app->add(JsonResponseMiddleware::class);