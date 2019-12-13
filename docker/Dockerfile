FROM php:7.3-fpm-alpine3.9

RUN apk add --no-cache --update tzdata
ENV TZ Europe/Amsterdam

ADD https://dl.bintray.com/php-alpine/key/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub
RUN apk --update add ca-certificates \
 && echo "https://dl.bintray.com/php-alpine/v3.9/php-7.3" >> /etc/apk/repositories

RUN apk --no-cache --update add \
    python \
    py-pip \
    openssl-dev \
    libzip-dev \
    libxml2-dev \
    gettext \
    bash \
    nginx \
    openrc \
    $PHPIZE_DEPS

RUN apk --no-cache --update add \
    php7 \
    php7-fpm \
    php7-ctype \
    php7-iconv \
    php7-json \
#    php7-pcre \
    php7-session \
    php7-xml \
#    php7-apcu \
    php7-opcache \
    php7-curl \
    php7-phar \
 && ln -s /usr/bin/php7 /usr/bin/php

RUN pecl install mongodb \
 && docker-php-ext-enable mongodb

RUN docker-php-ext-install \
    zip \
    soap

RUN mkdir -p /var/log/supervisor \
 && chmod -R 0777 /var/log/supervisor \
 && pip install supervisor
COPY ./docker/setup/supervisor/conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && chmod +x /usr/local/bin/composer

RUN mkdir -p /app/logs/consumers
RUN mkdir -p /app/logs/www
RUN rm -rf /etc/nginx/conf.d/default.conf
COPY ./docker/setup/nginx/conf.d/app.conf.template /etc/nginx/conf.d/app.conf
COPY ./docker/setup/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/setup/php/php.ini.template /usr/local/etc/php/php.ini

#COPY ./docker/php-fpm/setup/cron.d/symfony_cronjobs.template /etc/cron.d/symfony_cronjobs
#RUN crontab /etc/cron.d/symfony_cronjobs
#RUN chmod 0644 /etc/cron.d/symfony_cronjobs

WORKDIR /app/project
COPY ./ ./

RUN composer install --no-interaction --no-scripts --quiet
RUN chown -R www-data:www-data $(pwd)

#RUN adduser www-data root
#USER www-data

COPY ./docker/setup/init.sh /tmp/init.sh
CMD ["/bin/bash", "/tmp/init.sh"]
