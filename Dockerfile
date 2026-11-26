FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql mysqli mbstring \
    && a2enmod rewrite

WORKDIR /var/www/html
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

# Railway supplies PORT at runtime; make Apache listen on that exact port.
CMD ["sh", "-c", "PORT=${PORT:-8080}; sed -i \"s/Listen 80/Listen ${PORT}/\" /etc/apache2/ports.conf; sed -i \"s/:80>/:${PORT}>/\" /etc/apache2/sites-available/000-default.conf; exec apache2-foreground"]
