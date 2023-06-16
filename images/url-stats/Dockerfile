FROM debian:bullseye-slim

VOLUME ./url-stats /app

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN echo "deb-src http://security.debian.org/debian-security bullseye-security main" >> /etc/apt/sources.list

RUN apt-get update && apt-get install -y curl sqlite3 git zip unzip php7.4 php7.4-sqlite3 php7.4-curl php7.4-xml php7.4-zip;

RUN echo extension=curl >> /etc/php/7.4/cli/php.ini && \
    echo extension=xml >> /etc/php/7.4/cli/php.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer require doctrine/annotations && composer install

EXPOSE 80

CMD bash -c "composer require doctrine/annotations && \
    composer install && \
    /usr/bin/php7.4 -S 0.0.0.0:80 -t /app/public"