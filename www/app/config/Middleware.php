<?php
/**
 * Created by PhpStorm.
 * User: gbmcarlos
 * Date: 9/29/17
 * Time: 6:10 PM
 */

namespace App\config;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Middleware {

    public static function registerMiddlewares(Application $app) {

        $app->before(function (Request $request) {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : array());
            }
        });

    }

}