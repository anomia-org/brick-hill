#!/bin/sh

cd /var/www/html
php artisan package:discover
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force --database=migration
php artisan db:load-state
php artisan l5-swagger:generate
# octane devs decided to change the min version to something that isnt actually the min version
# and the latest version seems to introduce some wacky levels of latency on requests
# have to echo no to deny its prompt
echo "no" | php artisan octane:start --server=roadrunner --host=0.0.0.0 --workers=8 --max-requests=10000