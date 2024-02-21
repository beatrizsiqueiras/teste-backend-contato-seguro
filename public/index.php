<?php

require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

use Slim\Factory\AppFactory;

date_default_timezone_set('America/Sao_Paulo');

$app = AppFactory::create();

require_once dirname(__DIR__, 1) . '/src/Config/middlewares.php';
require_once dirname(__DIR__, 1) . '/src/Http/Route/routes.php';

$app->run();