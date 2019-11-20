FROM trafex/alpine-nginx-php7

WORKDIR /var/www/html/

COPY --chown=nobody:nobody ./ ./

USER root

RUN sed -i 's/root \/var\/www\/html;/root \/var\/www\/html\/public;/g' /etc/nginx/nginx.conf