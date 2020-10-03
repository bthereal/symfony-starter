#!/usr/bin/env bash
cp /etc/php7/php.ini /etc/php7/php-cli.ini
sed -i 's/memory_limit = .*/memory_limit = -1/' /etc/php7/php-cli.ini
sed -i 's/disable_functions = .*/disable_functions =/' /etc/php7/php-cli.ini
