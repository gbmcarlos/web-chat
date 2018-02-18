<?php
/**
 * Created by PhpStorm.
 * User: gbmcarlos
 * Date: 9/28/17
 * Time: 9:15 PM
 */

return [
    'CREATE TABLE IF NOT EXISTS user (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        createdAt INTEGER NOT NULL
    )',
    'CREATE TABLE IF NOT EXISTS chat (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user1Id REFERENCES user(id),
        user2Id REFERENCES user(id),
        createdAt INTEGER NOT NULL
    )',
    'CREATE TABLE IF NOT EXISTS message (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        chatId REFERENCES chat(id),
        senderId REFERENCES user(id),
        text STRING NOT NULL,
        createdAt INTEGER NOT NULL
    )'
];