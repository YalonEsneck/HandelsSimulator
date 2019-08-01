FROM php:apache

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
 && apt-get clean \
 && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
 && docker-php-ext-install -j$(nproc) gd

RUN rm /etc/apache2/sites-enabled/*
COPY files/config/sites-enabled /etc/apache2/sites-enabled
