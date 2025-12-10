FROM php:8.3-apache

# Sistem bağımlılıkları
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli bcmath tokenizer zip mbstring xml ctype json dom fileinfo openssl

# Apache mod rewrite
RUN a2enmod rewrite

# Proje dosyaları
COPY . /var/www/html/
WORKDIR /var/www/html/
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80
CMD ["apache2-foreground"]