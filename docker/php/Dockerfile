FROM alpine:3.8

RUN apk upgrade --update --no-cache && \
    apk add --update --no-cache \
    ca-certificates \
    curl \
    bash \
    make \
    bind-tools \
    git \
    php7 \
    php7-fpm \
    php7-cli \
    php7-common \
    php7-curl \
    php7-intl \
    php7-apcu \
    php7-calendar \
    php7-xsl \
    php7-mbstring \
    php7-gd \
    php7-openssl \
    php7-json \
    php7-simplexml \
    php7-dom \
    php7-pcntl \
    php7-sockets \
    php7-pdo \
    php7-tokenizer \
    php7-imagick \
    php7-iconv \
    php7-amqp \
    php7-mongodb \
    php7-mysqlnd \
    php7-pdo_mysql \
    php7-redis \
    php7-phar \
    php7-ctype \
    php7-xml \
    php7-xmlwriter \
    php7-xdebug \
    php7-soap \
    php7-opcache \
    php7-memcached \
    php7-pear \
    php7-dev \
    g++ \
    && rm -rf /var/cache/apk/*

RUN pecl install igbinary
RUN apk del g++ \
    php7-pear \
    php7-dev

RUN rm -rf /etc/php7/php.ini && \
    mkdir /var/www

COPY php.ini /etc/php7/php.ini
COPY www.conf /etc/php7/php-fpm.d/www.conf

RUN addgroup -g 1000 -S www-data && \
	adduser -u 1000 -D -S -h /var/www -s /sbin/nologin -G www-data www-data

RUN mkdir -p /var/log/php7

RUN chown -R www-data:www-data /var/log/php7

COPY config.sh /bin
RUN chmod +x /bin/config.sh
RUN bash /bin/config.sh

# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --filename=composer --install-dir=/bin
RUN php -r "unlink('composer-setup.php');"

WORKDIR /var/www

RUN composer global require "pyrech/composer-changelogs"

COPY start.sh /bin
RUN chmod +x /bin/start.sh

EXPOSE 9000
ENTRYPOINT ["/bin/start.sh"]
