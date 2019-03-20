FROM php:7.3.3-fpm-alpine

LABEL maintainer="Petr Zivny (petr.zivny@gmail.com)"

ARG APP_ENV

RUN apk update && apk upgrade                                       && \
    apk add --no-cache --virtual .build-dependencies $PHPIZE_DEPS   && \
    apk add --no-cache tini                                         && \
    apk del .build-dependencies                                     && \
    rm -rf /tmp/* /var/cache/apk/*

# ZIP
# Enables to transparently read or write ZIP compressed archives and the files inside them. $zip = new ZipArchive();
RUN apk --update add libzip-dev                 && \
    docker-php-ext-configure zip --with-libzip  && \
    docker-php-ext-install zip                  && \
    rm -rf /var/cache/apk/*

# OPcache
RUN apk update                                          && \
    docker-php-ext-configure opcache --enable-opcache   && \
    docker-php-ext-install opcache                      && \
    rm -rf /var/cache/apk/*
COPY .docker/php/conf.d/00-opcache-recommended-$APP_ENV.ini /usr/local/etc/php/conf.d/00-opcache.ini
COPY .docker/php/conf.d/opcache-blacklist.txt /usr/local/etc/php/conf.d/opcache-blacklist.txt

# APCu
RUN apk --update add --virtual .build-dependencies autoconf g++ make    && \
    pecl install apcu                                                   && \
    docker-php-ext-enable apcu                                          && \
    apk del .build-dependencies && rm -rf /var/cache/apk/*
COPY .docker/php/conf.d/00-apcu-recommended.ini /usr/local/etc/php/conf.d/00-apcu-recommended.ini

# PDO MySQL
# Driver to enable access from PHP to MySQL databases. Implements PDO (doesn't need it). $dbh = new PDO($dsn, $user, $password);
RUN apk update                                                          && \
    docker-php-ext-install -j"$(getconf _NPROCESSORS_ONLN)" pdo_mysql   && \
    rm -rf /var/cache/apk/*

ENV COMPOSER_ALLOW_SUPERUSER=1
# Development environment only (or fresh install)
RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/conf.d/php.ini                     && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer  && \
    rm -rf /tmp/* /var/cache/apk/*

# to use aliases run docker exec -ituroot {php_container_name} sh -l
COPY .docker/php/profile.d/aliases.sh /etc/profile.d/

CMD ["php-fpm"]
