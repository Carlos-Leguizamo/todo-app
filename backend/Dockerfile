# Usa una imagen que contenga PHP 8.1
FROM richarvey/nginx-php-fpm:latest

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos de tu aplicación al contenedor
COPY . .

# Configuración de imagen
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Configuración de Laravel
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Permitir que Composer se ejecute como root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala las dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Copia el script de inicio y asegúrate de que sea ejecutable
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Comando de inicio
CMD ["/start.sh"]
