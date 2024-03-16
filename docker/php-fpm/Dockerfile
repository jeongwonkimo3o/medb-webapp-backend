FROM php:8.2-fpm

# 필요한 패키지 인스톨
RUN apt-get update && apt-get install -y zlib1g-dev mariadb-client vim libzip-dev unzip p7zip && docker-php-ext-install zip pdo_mysql

# Composer 설치
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

WORKDIR /var/www

# 먼저 Composer 종속성 파일 복사
COPY composer.json composer.lock ./

# Composer 종속성 설치
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader

# 나머지 소스 코드 복사
COPY . .
