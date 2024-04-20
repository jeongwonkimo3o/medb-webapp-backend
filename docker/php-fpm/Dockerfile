FROM php:8.2-fpm

# 필요한 패키지 인스톨
RUN apt-get update \
  && apt-get install -y zlib1g-dev mariadb-client vim libzip-dev unzip p7zip \
  && docker-php-ext-install zip pdo_mysql

# Composer install
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

# Composer 설정
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

# Laravel Installer를 글로벌로 설치
RUN composer global require "laravel/installer"

WORKDIR /var/www

COPY . .

# Laravel 프로젝트의 의존성 설치 및 오토로딩 최적화
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader