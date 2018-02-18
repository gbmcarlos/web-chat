<?php

namespace App\config;

use App\controllers\FrontController;
use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;

/**
 * Created by PhpStorm.
 * User: gbmcarlos
 * Date: 11/6/17
 * Time: 11:28 AM
 */
class Services {

    public static function registerServices(Application $app) {

        // To register controllers as services (with DI)
        $app->register(new ServiceControllerServiceProvider());

        // Register the services and controllers here

        // Controllers
        // Front controller
        $app['FrontController'] = function() use ($app) {
            return new FrontController(
                $app['twig']
            );
        };

        // Services
        // Twig
        $app->register(new TwigServiceProvider(), array(
            'twig.path' => __DIR__.'/../templates',
        ));
        // Twig bridge, gives access to functions like url and path in the templates
        $app->register(new RoutingServiceProvider());

        //Symfony's assets manager
        $app->register(new AssetServiceProvider(array(
            'assets.version' => 'v1',
            'assets.base_path' => '/assets'
        )));

    }

}