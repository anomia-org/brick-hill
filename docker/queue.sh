#!/bin/sh

cd /var/www/html
php artisan package:discover
/usr/bin/supervisord -c /etc/supervisord.conf