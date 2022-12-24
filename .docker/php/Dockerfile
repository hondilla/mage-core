FROM php:8.2.0-cli-alpine

ARG UID
ARG GID

RUN apk --update --no-cache add \
    curl \
    git \
    icu-dev

RUN apk --update --no-cache add --virtual deps $PHPIZE_DEPS linux-headers \
&& pecl install -f xdebug \
&& docker-php-ext-install intl \
&& docker-php-ext-enable xdebug intl \
&& apk del --purge deps

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "xdebug.start_with_request=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "opcache.enable=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
 && echo "opcache.enable_cli=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
 && echo "opcache.jit=1255" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
 && echo "opcache.jit_buffer_size=256MB" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

RUN addgroup -S -g ${GID} app \
 && adduser -S -D -u ${UID} -s /sbin/nologin -h /home/app -G app app \
 && chown -R app:app /home/app \
 && chmod -R g+w /home/app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN mkdir -p /home/app/.composer \
 && chmod -R g+w /home/app/.composer

USER app
WORKDIR /app