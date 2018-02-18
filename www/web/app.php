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

// Set up db connections and set schema
$db = new DB\SQLiteDatabase();
$db->connect(getenv('SQLITEDB_FILE'));
$schema = require_once(__DIR__ . '/../db/schema.php');
$db->create($schema);

// Register db in the service container
$app['DB'] = function () use ($db) {
    return $db->getPDO();
};

// Register app service, similar to registering the bundles in the app kernel in symfony
\App\config\Services::registerServices($app);

\App\config\Routing::registerRoutes($app);

\App\config\Middleware::registerMiddlewares($app);

$app->run();