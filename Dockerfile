FROM php:8.1-fpm-bullseye

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/test

COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/
COPY docker/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY docker/php/php.ini /usr/local/etc/php/php.ini

RUN pecl install xdebug-3.1.0beta2

RUN apt update && apt install -y \
    vim \
    git \
    libzip-dev \
    libicu-dev \
    zip \
    libssh-dev \
    make \
    libcurl3-dev \
    libgmp-dev \
    libpq-dev

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

RUN docker-php-ext-install \
    pdo_pgsql \
    zip \
    bcmath \
    intl \
    curl \
    gmp

RUN docker-php-ext-enable xdebug

RUN (umask 000; touch /var/log/xdebug.log)

RUN useradd -ms /bin/bash test
RUN usermod -u 1000 test

RUN touch /var/log/php-fpm.error.log
RUN touch /var/log/php-fpm.access.log

RUN chown -R test:test /var/log/php-fpm.error.log /var/log/php-fpm.access.log

USER test

CMD ["php-fpm"]
