<?php
/**
 * Created by PhpStorm.
 * User: gbmcarlos
 * Date: 9/27/17
 * Time: 5:07 AM
 */

require_once __DIR__ . '/../../vendor/autoload.php';

// Create app
$app = new Silex\Application();

// Set debug from env var
$app['debug'] = getenv('APP_DEBUG');

// Register app service, similar to registering the bundles in the app kernel in symfony
\App\config\Services::registerServices($app);

\App\config\Routing::registerRoutes($app);

$app->run();