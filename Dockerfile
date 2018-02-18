FROM php:7.1-apache

##### INSTALLATION

#### Install system dependencies
RUN    apt-get update \
    && apt-get -yq install \
        curl \
        libapache2-mod-macro \
        git \
        zip \
        libpng12-dev libjpeg-dev \
    && rm -rf /var/lib/apt/lists/*
## Install and enable Xdebug
RUN pecl install xdebug-2.5.5 && docker-php-ext-enable xdebug

#### Install application dependencies
###
# To avoid cache misses everytime the source code changes,
# we first copy composer files (specially json and phar), then install the dependencies
# later on (latest possible), we copy the source code and dump the autoload
###

## Copy composer json, lock and phar
COPY ./composer.* /var/www/
## Install the dependences
RUN php /var/www/composer.phar install --no-scripts --no-autoloader --working-dir=/var/www

##### CONFIGURATION

#### Read build arguments, with default values
### Apache
ARG CONTAINER_PORT=80
ARG APACHE_USER=root
ARG HOST_NAME="app.com"
### Application
ARG APP_ENV=prod
ARG APP_DEBUG=false
### XDebug
ARG XDEBUG_ENABLE=off
ARG XDEBUG_IDEKEY=idekey
ARG XDEBUG_REMOTE_PORT=9000
ARG XDEBUG_REMOTE_HOST=localhost

#### Set environment variables (These will be persisted in the image, so available in the container)
### Apache
ENV PORT $CONTAINER_PORT
ENV HOST_NAME $HOST_NAME
### Application
ENV APP_ENV $APP_ENV
ENV APP_DEBUG $APP_DEBUG

#### Configure Xsebug
RUN echo '\n\
xdebug.remote_enable='$XDEBUG_ENABLE'\n\
xdebug.remote_enable='$XDEBUG_IDEKEY'\n\
xdebug.remote_port='$XDEBUG_REMOTE_PORT'\n\
xdebug.remote_host='$XDEBUG_REMOTE_HOST'\n\
xdebug.remote_autostart=on\n\
xdebug.remote_connect_back=off\n\
xdebug.remote_handler=dbgp\n\
xdebug.profiler_enable=0\n'\
>> /usr/local/etc/php/conf.d/xdebug.ini

#### Configure OPcache
RUN echo '\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=20000\n\
opcache.revalidate_freq=60\n\
opcache.fast_shutdown=1\n\
opcache.enable_cli=1\n\
realpath_cache_size=4096K\n\
realpath_cache_ttl=600\n'\
> /usr/local/etc/php/conf.d/opcache-recommended.ini

#### Configure Apache
###
# To make the port Apache listens on configurable,
# we use a macro as the virtual host configuration
# and comment the line to listen port 80 by default
###

## Copy virtual host macro
COPY ./deploy/apache/main.conf /etc/apache2/sites-available/main.conf

## Enable rewrite and macro module
RUN a2enmod rewrite macro

## Enable the site
RUN a2dissite 000-default && a2ensite main

## Don't listen by default port 80
RUN sed -i 's/^Listen 80/#Listen80/' /etc/apache2/ports.conf

## Copy the application's source code
COPY ./www /var/www/www
## And now dump the autoload
RUN php /var/www/composer.phar dump-autoload --optimize  --working-dir=/var/www

## Copy Apache run script (this is the script that will be run as entrypoint, i.e. the script to run to start the container)
COPY ./deploy/apache/run.sh  /run.sh
RUN chmod 777 /run.sh
USER $APACHE_USER

CMD ["/run.sh"]
