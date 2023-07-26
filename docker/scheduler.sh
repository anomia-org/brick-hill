#!/bin/sh

cd /var/www/html
/usr/bin/crontab docker/cron/scheduler.txt
/usr/sbin/crond -f -d 8