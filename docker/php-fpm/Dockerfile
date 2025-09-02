FROM php:8.2-fpm-alpine

# Устанавливаем системные зависимости
RUN apk update && apk add --no-cache \
    curl \
    git \
    unzip \
    libzip-dev \
    zip \
    && docker-php-ext-install zip

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Рабочая директория
WORKDIR /var/www/html

# Копируем composer файлы для кэширования
COPY composer.json composer.lock ./

# Устанавливаем зависимости
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Копируем весь код
COPY . .

# Настраиваем права
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p var/log && chmod -R 777 var/log

# Запускаем PHP-FPM
CMD ["php-fpm", "-F"]