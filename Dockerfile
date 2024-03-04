FROM php:8.2-cli

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install -y \
    unzip

WORKDIR /var/www/html/api

COPY /.docker/entrypoint.sh ./.docker

ENTRYPOINT ["sh", "./.docker/entrypoint.sh"]

EXPOSE 8000
