FROM phpearth/php:7.3-nginx

# install deps
RUN apk add --no-cache git \
    && apk add --no-cache composer

# adjust nginx
COPY docker/default.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf
RUN sed -i "s|upload_max_filesize = 2M|upload_max_filesize = 1024M|g" /etc/php/7.3/php.ini \
    && sed -i "s|post_max_size = 8M|post_max_size = 1024M|g" /etc/php/7.3/php.ini \
    && sed -i "s|max_execution_time = 30|max_execution_time = 86400|g" /etc/php/7.3/php.ini

# copy application and set permissions
COPY config/ /var/www/html/application/config/
COPY data/ /var/www/html/application/data/
COPY public/ /var/www/html/application/public/
COPY src/ /var/www/html/application/src/
COPY composer.json /var/www/html/application/composer.json
RUN mkdir -p /var/www/html/application/bin/ \
    && mkdir -p /var/www/html/application/cache/ \
    && chown -R www-data:www-data /var/www/html/application

# initialize app
RUN cd /var/www/html/application/ \
    && composer install