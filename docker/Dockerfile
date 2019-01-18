FROM phpearth/php:7.3-nginx

# install deps
RUN apk add --no-cache git
RUN apk add --no-cache composer

# adjust nginx
COPY docker/default.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf

# copy application and set permissions
RUN mkdir -p /var/www/html/ts3web/bin/
COPY config/ /var/www/html/ts3web/config/
RUN mkdir -p /var/www/html/ts3web/cache/
ADD data/ /var/www/html/ts3web/data/
RUN chmod -R 777 /var/www/html/ts3web/data/
RUN mkdir -p /var/www/html/ts3web/log/
RUN touch /var/www/html/ts3web/log/application.log
RUN chmod 777 /var/www/html/ts3web/log/application.log
ADD public/ /var/www/html/ts3web/public/
ADD src/ /var/www/html/ts3web/src/
ADD composer.json /var/www/html/ts3web/composer.json

# initialize app
RUN cd /var/www/html/ts3web/ && composer install