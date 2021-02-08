FROM alpine:3

LABEL maintainer="Varakh<varakh@varakh.de>"

ENV APP_HOME /var/www/html/application

# setup folder structure
RUN mkdir -p ${APP_HOME}/data/snapshots && \
    mkdir -p ${APP_HOME}/log && \
    touch ${APP_HOME}/log/application.log && \
    mkdir -p ${APP_HOME}/config

# add upstream application
ADD src ${APP_HOME}/src
ADD public ${APP_HOME}/public
ADD composer.json ${APP_HOME}/composer.json
ADD composer.lock ${APP_HOME}/composer.lock
ADD data ${APP_HOME}/data
ADD config ${APP_HOME}/config
RUN mv ${APP_HOME}/config/env.example ${APP_HOME}/config/env

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
    cd ${APP_HOME} && composer install && \
    # clean up and permissions
    rm -rf /var/cache/apk/* && \
    chown nobody:nginx -R ${APP_HOME}

# Add nginx config
ADD docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

# add overlay
ADD docker/s6 /etc/s6/

# expose start
CMD exec s6-svscan /etc/s6/
