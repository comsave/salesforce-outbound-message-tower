FROM trafex/alpine-nginx-php7

USER root

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

RUN apk update && \
    apk add \
       php-simplexml \
       php-iconv

COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/php-fpm.conf /etc/php7/php-fpm.d/www.conf

COPY ./docker/crontab /var/www/crontab
RUN crontab /var/www/crontab

WORKDIR /var/www/html/

COPY --chown=nobody:nobody ./ ./

RUN composer install --no-dev