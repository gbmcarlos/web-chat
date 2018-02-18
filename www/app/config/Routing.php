<?php
/**
 * Created by PhpStorm.
 * User: gbmcarlos
 * Date: 9/29/17
 * Time: 4:27 AM
 */

namespace App\config;

use Silex\Application;

class Routing {

    public static function registerRoutes(Application $app) {

        // front pages
        $app->get('/', function() use ($app) {
            return $app->redirect('/login');
        })->bind('home_page');
        $app->get('/login', "FrontController:loginAction")
            ->bind('login_page');
        $app->get('/{username}/dashboard', "FrontController:dashboardAction")
            ->bind('dashboard_page');
        $app->get('/{username1}/chat/{username2}', "FrontController:chatAction")
            ->bind('chat_page');

        // api endpoints
        // user
        $app->post('/api/login/{username}', "ApiController:login")
            ->bind('login');

        // chat
        $app->post('/api/{username1}/create_new_chat/{username2}', "ApiController:createNewChat")
            ->bind('createNewChat');

        $app->post('/api/{username1}/create_new_message/{username2}', "ApiController:createNewMessage")
            ->bind('createNewMessage');

        $app->get('/api/{username1}/get_chat_messages_since/{username2}/{lastMessageId}', "ApiController:getChatMessagesSince")
            ->bind('getChatMessagesSince');

    }

}