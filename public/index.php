<?php

require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

require_once dirname(__DIR__, 1) . '/src/Config/middlewares.php';
require_once dirname(__DIR__, 1) . '/src/Route/routes.php';

$app->run();