<?php

namespace App\controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use App\services\ChatRepository;
use App\services\UserRepository;

/**
 * Created by PhpStorm.
 * User: gbmcarlos
 * Date: 9/28/17
 * Time: 10:59 PM
 */
class FrontController {

    protected $userRepo;
    protected $chatRepo;
    protected $twig;

    public function __construct(UserRepository $userRepository, ChatRepository $chatRepository, $twig) {
        $this->userRepo = $userRepository;
        $this->chatRepo = $chatRepository;
        $this->twig = $twig;
    }

    public function loginAction(Application $app, Request $request) {
        return $this->twig->render('loginPage.twig');
    }

    public function dashboardAction($username, Application $app, Request $request) {

        $user = $this->userRepo->getUser($username);

        if ($user) {

            $chats = $this->userRepo->getUserChats($user['id']);

            return $this->twig->render('dashboardPage.twig', array(
                'username' => $username,
                'chats' => $chats
            ));

        } else {
            return $this->twig->render('errorPage.twig', array(
                'error_message' => "User '$username' does not exists"
            ));
        }

    }

    public function chatAction($username1, $username2, Request $request) {

        $user1 = $this->userRepo->getUser($username1);
        $user2 = $this->userRepo->getUser($username2);

        if ($user1 && $user2) {

            $chat = $this->chatRepo->getChatByUserIds($user1['id'], $user2['id']);

            if ($chat) {

                $messages = $this->chatRepo->getChatMessages($chat['id']);

                foreach ($messages as $index => $message) {

                    $messages[$index]['sent'] = $message['senderId'] == $user1['id'];

                }

                return $this->twig->render('chatPage.twig', array(
                    'userId' => $user1['id'],
                    'username1' => $username1,
                    'username2' => $username2,
                    'messages' => $messages
                ));
            }

        }
        return $this->twig->render('errorPage.twig', array(
            'error_message' => "Invalid chat between user '$username1' and '$username2'"
        ));

    }

}