#!/usr/bin/env bash

export HOST_PORT=${HOST_PORT:=80}
export CONTAINER_PORT=${INSIDE_PORT:=80}
export APACHE_USER=${APACHE_USER:=root}
export HOST_NAME=${HOST_NAME:=app.com}

export APP_ENV=${APP_ENV:=local}
export APP_DEBUG=${APP_DEBUG:=true}

export XDEBUG_ENABLE=${XDEBUG_ENABLE:=on}
export XDEBUG_IDEKEY=${XDEBUG_IDEKEY:=idekey}
export XDEBUG_REMOTE_PORT=${XDEBUG_REMOTE_PORT:=9000}
export XDEBUG_REMOTE_HOST=${XDEBUG_REMOTE_HOST:=localhost}

echo ">>>> Moving to $(dirname "$0")"
cd "$(dirname "$0")"
echo ">>>> Building docker image"
docker build \
    --build-arg CONTAINER_PORT=$CONTAINER_PORT \
    --build-arg APACHE_USER=$APACHE_USER \
    --build-arg HOST_NAME=$HOST_NAME \
    --build-arg APP_ENV=$APP_ENV \
    --build-arg APP_DEBUG=$APP_DEBUG \
    --build-arg XDEBUG_ENABLE=$XDEBUG_ENABLE \
    --build-arg XDEBUG_REMOTE_PORT=$XDEBUG_REMOTE_PORT \
    --build-arg XDEBUG_REMOTE_HOST=$XDEBUG_REMOTE_HOST \
    -t web-chat:latest $PWD/..

echo ">>>> Removing old container"
docker rm -f web-chat || true

echo ">>>> Running new container"
docker run --name web-chat -e SQLITEDB_FILE=/var/www/html/phpsqlte.db -d -p $HOST_PORT:$CONTAINER_PORT -v $PWD/../www:/var/www/www web-chat:latest

echo ">>>> Tailing logs"
docker logs -f web-chat
