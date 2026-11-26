FROM php:8.3-apache

# Install the build tools and Oniguruma headers required by mbstring.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev pkg-config \
    && docker-php-ext-install pdo_mysql mysqli mbstring \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

# Railway supplies PORT at runtime; configure both Apache listener and virtual host.
CMD ["sh", "-c", "PORT=${PORT:-8080}; sed -i \"s/^Listen .*/Listen ${PORT}/\" /etc/apache2/ports.conf; sed -i \"s/<VirtualHost \\*:.*>/<VirtualHost *:${PORT}>/\" /etc/apache2/sites-available/000-default.conf; exec apache2-foreground"]
