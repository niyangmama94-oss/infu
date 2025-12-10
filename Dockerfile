FROM php:8.3-apache

# Sistem paketleri
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli bcmath tokenizer zip mbstring xml ctype json dom fileinfo openssl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Apache mod rewrite
RUN a2enmod rewrite

# Proje dosyalarını kopyala
COPY . /var/www/html/
WORKDIR /var/www/html/
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80
CMD ["apache2-foreground"]