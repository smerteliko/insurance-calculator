
FROM php:8.2-fpm-alpine

RUN apk update && apk add --no-cache \
    curl \
    git \
    unzip \
    libzip-dev \
    zip \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN if [ -f composer.lock ]; then \
      composer install --no-dev --no-interaction --optimize-autoloader; \
    else \
      composer update --no-dev --no-interaction --optimize-autoloader; \
    fi

COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p var/log && chmod -R 777 var/log

CMD ["php-fpm", "-F"]