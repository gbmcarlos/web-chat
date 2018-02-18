# Bunq Back End Engineer assignment
[chat.gbmcarlos.com](http://chat.gbmcarlos.com)

## Installation

Install dependencies with `composer install`.

Build the Docker image (first time) with `docker build -t chat_app:latest [project's root directory]`.

Run the Docker container with `docker run --name chat_app -e APP_ENV=[env] -e SQLITEDB_FILE=[db_file] -e APP_DEBUG=[debug] -d -p [port]:80 -v [project's root directory]/www:/var/www/html chat_app:latest`.

*Note: PHP will need premissions to access the file specified in `SQLITEDB_FILE`

and visit `http://[docker ip]:[port]/` for testing the application.

OR

run `./deploy/up.sh`. This will build and run the application locally, with debug on port 80 and tail the logs

## Assignment
*Write a very simple 'chat' application backend in PHP. A user should be able to send a simple text
message to another user and a user should be able to get the messages sent to him and the
author users of those messages. The users and messages should be stored in a simple SQLite
database. All communication between the client and server should happen over a simple JSON
based protocol over HTTP (which may be periodically refreshed to poll for new messages). A GUI,
user registration and user login are not needed but the users should be identified by some token
or ID in the HTTP messages and the database. You have the freedom to use any framework and
libraries; keep in mind though that we love custom-build.*

## Requirements

### User journey

#### Login page

In this page the user will have a text box to input the username and a submit button.
The username can be an alphanumeric string. 

* When the submit is clicked, the user will be redirected to the dashboard page.
* If a user with that username does not exists, it will be created.

#### Dashboard page

In this page the user will be shown a list of users he has had messages with (existing chats), and a text box and a button to start a new chat.

* When one of the existing chats is clicked, the user will be redirected to the chat page with that other user.
* When the new chat button is clicked, if the textbox contains a valid username, the user will be redirected to a blank chat page.

#### Chat page

In this page the user will see the username of the other user, and all the messages sent and received in that chat, with the time they were sent and received; and a text area and a button to send a new message.

* When the new message button is clicked, the new message will be sent and it will appear in the list of messages.

* The list of messages will be automatically refreshed every minute.

### Tech stack

* Apache server
* PHP
* Silex framework
* SQLite database
* Docker container

## Tech actions

* Project set up
    * Docker container with Apache and PHP
    * Silex application as a docker volume
* Define database schema
    * User table: ID (unique key, aut-increment, not null), username (unique, not null), createdAt (dateTime, not null)
    * Chat table: ID (unique key, aut-increment, not null), user1Id (foreign key), user2Id (foreign key), createdAt (dateTime not null)
        * a user can have many chats (one-to-many), but a chat always have 2 users (one-to-two?)(we are not doing group chats)
    * Message table: ID (unique key, aut-increment, not null), chatId (foreign key), text (string, not null), createdAt (dateTime not null)
* Implement endpoints to:
    * Check if user exists by username
    * Create new user with username
    * Retrieve list of existing chats for a user, each with the last message
    * Retrieve list of messages for a chat
    * Retrieve list of messages for a chat since last messages
    * Send new message
* Implement templates
    * Login: form with text box and submit button
    * Dashboard: list iterating through the chats, form with text box and submit button
    * Chat: List of messages, send and received differentiated by a class, each with the text and the time, text area and submit button 
* Implement front-end logic:
    * Before submitting the login form, check that the username is a valid string
    * Before submitting the new chat form, AJAX call to check if the user exists (nice to have; also in the backend)
    * AJAX call to send the new message
    * Automatic update of messages
    
*Note on urls: Since there will not be authentication nor authorzation, the applicatioon will not keep a user session. Therefore, the URLs will follow a RESTful approach:*

* /login (login page)
* /{username}/dashboard (dashboard page for a specific user)
* /{username}/chat/{chatId} (chat page for a specific chat of a specific user)
    
