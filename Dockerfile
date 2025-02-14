FROM php:8.2-apache

# Instalacja wymaganych pakietów
RUN apt-get update && apt-get install -y \
    libpng-dev zip unzip curl gnupg \
    && docker-php-ext-install pdo pdo_mysql

# Instalacja Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Konfiguracja Apache, aby wskazywał na `public/`
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite

# Ustawienie katalogu roboczego
WORKDIR /var/www/html

# Kopiowanie plików Laravel **z ustawieniem właściciela**
COPY --chown=www-data:www-data ./app /var/www/html

# Tworzenie wymaganych katalogów
RUN mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Eksponowanie portu 80
EXPOSE 80

# Uruchomienie Apache
CMD ["apache2-foreground"]
