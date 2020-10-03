#!/usr/bin/env bash
if [[ $XDEBUG_ENABLED == true ]]; then
    reverseDNS=$(host host.docker.internal)
    if [[ $? != 0 ]]; then
        REMOTE_HOST=`/sbin/ip route|awk '/default/ { print $3 }'`
    else
        REMOTE_HOST="host.docker.internal"
    fi

    echo "XDebug connecting to: $REMOTE_HOST on port 9000"

    echo "[xdebug]" > /etc/php7/conf.d/00_xdebug.ini
    echo "zend_extension=/usr/lib/php7/modules/xdebug.so" >> /etc/php7/conf.d/00_xdebug.ini
    echo "xdebug.remote_enable=1" >> /etc/php7/conf.d/00_xdebug.ini
    echo "xdebug.remote_connect_back=0" >> /etc/php7/conf.d/00_xdebug.ini
    echo "xdebug.remote_host=$REMOTE_HOST" >> /etc/php7/conf.d/00_xdebug.ini
    echo "xdebug.remote_autostart=1" >> /etc/php7/conf.d/00_xdebug.ini
    echo "xdebug.remote_port=9000" >> /etc/php7/conf.d/00_xdebug.ini
    echo 'xdebug.remote_handler=dbgp' >> /etc/php7/conf.d/00_xdebug.ini
    echo 'xdebug.idekey=PASSENGER' >> /etc/php7/conf.d/00_xdebug.ini
else
    echo "XDebug Disabled"
fi

/usr/sbin/php-fpm7 -F
