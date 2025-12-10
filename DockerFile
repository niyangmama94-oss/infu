FROM php:8.3-apache

# Sistemi güncelle
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        bcmath \
        ctype \
        curl \
        dom \
        fileinfo \
        gd \
        mbstring \
        mysqli \
        pdo \
        pdo_mysql \
        tokenizer \
        xml \
        zip

# Apache config
RUN a2enmod rewrite

# Projeyi Apache web root'a kopyala
COPY . /var/www/html/

# Apache izinleri
RUN chown -R www-data:www-data /var/www/html

# allow_url_fopen gibi php.ini ayarları
RUN echo "allow_url_fopen=On" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "file_uploads=On" >> /usr/local/etc/php/conf.d/custom.ini

EXPOSE 80
