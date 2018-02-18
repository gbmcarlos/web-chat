<?php

namespace App\services;

/**
 * Created by PhpStorm.
 * User: gbmcarlos
 * Date: 9/28/17
 * Time: 11:04 PM
 */
class ChatRepository {

    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function checkChatExists($user1Id, $user2Id) :bool {

        $stmt = $this->pdo->prepare("SELECT * FROM chat as c WHERE (c.user1Id = :user1Id AND c.user2Id = :user2Id) OR (c.user1Id = :user2Id AND c.user2Id = :user1Id)");
        if (!$stmt) {
            throw new \Exception($this->pdo->errorInfo()[2]);
        }
        $stmt->bindParam('user1Id', $user1Id);
        $stmt->bindParam('user2Id', $user2Id);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $result = count($result) >= 1;

        return $result;

    }

    public function createNewChat($user1Id, $user2Id) : bool {

        $now = (new \DateTime())->getTimestamp();

        $stmt = $this->pdo->prepare("INSERT INTO chat(user1Id, user2Id, createdAt) VALUES (:user1Id, :user2Id, :now)");
        if (!$stmt) {
            throw new \Exception($this->pdo->errorInfo()[2]);
        }
        $stmt->bindParam('user1Id', $user1Id);
        $stmt->bindParam('user2Id', $user2Id);
        $stmt->bindParam('now', $now);
        $stmt->execute();

        $result = $stmt->rowCount() == 1;

        return $result;

    }

    public function getChat($chatId) {

        $stmt = $this->pdo->prepare("SELECT * FROM chat WHERE chat.id = :chatId");
        if (!$stmt) {
            throw new \Exception($this->pdo->errorInfo()[2]);
        }
        $stmt->bindParam('chatId', $chatId);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result;

    }

    public function getChatByUserIds($user1Id, $user2Id) {

        $stmt = $this->pdo->prepare("SELECT * FROM chat WHERE (chat.user1Id = :user1Id AND chat.user2Id = :user2Id) OR (chat.user1Id = :user2Id AND chat.user2Id = :user1Id)");
        if (!$stmt) {
            throw new \Exception($this->pdo->errorInfo()[2]);
        }
        $stmt->bindParam('user1Id', $user1Id);
        $stmt->bindParam('user2Id', $user2Id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result;

    }

    public function createNewMessage($chatId, $senderId, $text) {

        $now = (new \DateTime())->getTimestamp();

        $stmt = $this->pdo->prepare("INSERT INTO message(chatId, senderId, text, createdAt) VALUES (:chatId, :senderId, :text, :now)");
        if (!$stmt) {
            throw new \Exception($this->pdo->errorInfo()[2]);
        }
        $stmt->bindParam('chatId', $chatId);
        $stmt->bindParam('senderId', $senderId);
        $stmt->bindParam('text', $text);
        $stmt->bindParam('now', $now);
        $stmt->execute();

        $id = $this->pdo->lastInsertId();

        $result = array(
            'id' => $id,
            'chatId' => $chatId,
            'senderId' => $senderId,
            'text' => $text,
            'createdAt' => $now
        );

        return $result;

    }

    public function getChatMessages($chatId) {

        $stmt = $this->pdo->prepare("SELECT * FROM message WHERE message.chatId = :chatId");
        if (!$stmt) {

            throw new \Exception($this->pdo->errorInfo()[2]);
        }
        $stmt->bindParam('chatId', $chatId);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;

    }

    public function getChatMessagesSince($chatId, $lastMessageId) {

        $stmt = $this->pdo->prepare("SELECT * FROM message WHERE message.chatId = :chatId AND id > :lastMessageId");
        if (!$stmt) {
            throw new \Exception($this->pdo->errorInfo()[2]);
        }
        $stmt->bindParam('chatId', $chatId);
        $stmt->bindParam('lastMessageId', $lastMessageId);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;

    }

}