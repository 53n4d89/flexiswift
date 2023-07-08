<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Psr\Container\ContainerInterface;
use App\middleware\CorsMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// Create Container
$container = new Container();

AppFactory::setContainer($container);

// Instantiate the app
$app = AppFactory::create();

// Load settings
$settings = require __DIR__ . '/../config/settings.php';

// Register the settings in the container
$container->set('settings', $settings);

// Set up dependencies
$dependencies = require __DIR__ . '/../config/dependencies.php';
$dependencies($container);

// Register routes
$routes = require __DIR__ . '/../routes/api.php';
$routes($app);

$app->add(new CorsMiddleware());

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Run app
$app->run();
