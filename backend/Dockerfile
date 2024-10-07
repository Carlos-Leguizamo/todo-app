# Usa una imagen que contenga PHP 8.1# FROM richarvey/nginx-php-fpm:1.7.2FROM richarvey/nginx-php-fpm:latestCOPY . .
# Image configENV SKIP_COMPOSER 1ENV WEBROOT /var/www/html/publicENV PHP_ERRORS_STDERR 1ENV RUN_SCRIPTS 1ENV REAL_IP_HEADER 1
# Laravel configENV APP_ENV productionENV APP_DEBUG falseENV LOG_CHANNEL stderr
# Allow composer to run as rootENV COMPOSER_ALLOW_SUPERUSER 1
CMD ["/start.sh"]
