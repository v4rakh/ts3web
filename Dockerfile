FROM alpine:3

LABEL maintainer="Varakh<varakh@varakh.de>"

# setup folder structure
RUN mkdir -p /var/www/data/snapshots && \
    mkdir -p /var/www/log && \
    touch /var/www/log/application.log && \
    mkdir -p /var/www/config

# add upstream application
ADD src /var/www/src
ADD public /var/www/public
ADD composer.json /var/www/composer.json
ADD composer.lock /var/www/composer.lock
ADD data /var/www/data
ADD config /var/www/config
RUN mv /var/www/config/env.example /var/www/config/env

# php.ini
ENV PHP_MEMORY_LIMIT    512M
ENV MAX_UPLOAD          1024M
ENV PHP_MAX_FILE_UPLOAD 200
ENV PHP_MAX_POST        1024M

# install dependencies
RUN apk add --update --no-cache \
        nginx \
        s6 \
        curl \
        git \
        composer \
        php7 \
        php7-fpm \
        php7-cli \
        php7-intl \
        php7-curl \
        php7-json \
        php7-dom \
        php7-simplexml \
        php7-pcntl \
        php7-posix \
        php7-mcrypt \
        php7-session \
        php7-gd \
        php7-phar \
        php7-fileinfo \
        php7-mbstring \
        php7-ctype \
        php7-ldap \
        php7-pecl-memcached \
        memcached \
        ca-certificates && \
    rm -rf /var/cache/apk/* && \
    apk add gnu-libiconv --update-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/community/ --allow-untrusted && \
    # set environments
    sed -i "s|;*memory_limit =.*|memory_limit = ${PHP_MEMORY_LIMIT}|i" /etc/php7/php.ini && \
    sed -i "s|;*upload_max_filesize =.*|upload_max_filesize = ${MAX_UPLOAD}|i" /etc/php7/php.ini && \
    sed -i "s|;*max_file_uploads =.*|max_file_uploads = ${PHP_MAX_FILE_UPLOAD}|i" /etc/php7/php.ini && \
    sed -i "s|;*post_max_size =.*|post_max_size = ${PHP_MAX_POST}|i" /etc/php7/php.ini && \
    # prepare application
    cd /var/www && composer install && \
    # clean up and permissions
    rm -rf /var/cache/apk/* && \
    chown nobody:nginx -R /var/www

# Add nginx config
ADD docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

# add overlay
ADD docker/s6 /etc/s6/

# expose start
CMD exec s6-svscan /etc/s6/
