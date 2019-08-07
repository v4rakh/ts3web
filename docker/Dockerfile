FROM phpearth/php:7.3-nginx

# install deps
RUN apk add --no-cache git \
    && apk add --no-cache composer

# adjust nginx
COPY docker/default.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf

# copy application and set permissions
COPY config/ /var/www/html/application/config/
COPY data/ /var/www/html/application/data/
COPY public/ /var/www/html/application/public/
COPY src/ /var/www/html/application/src/
COPY composer.json /var/www/html/application/composer.json
RUN mkdir -p /var/www/html/application/bin/ \
    && mkdir -p /var/www/html/application/cache/ \
    && chmod -R 777 /var/www/html/application/data/ \
    && mkdir -p /var/www/html/application/log/ \
    && touch /var/www/html/application/log/application.log \
    && chmod 777 /var/www/html/application/log/application.log \
    && chown -R www-data:www-data /var/www/html/application

# initialize app
RUN cd /var/www/html/application/ \
    && composer install