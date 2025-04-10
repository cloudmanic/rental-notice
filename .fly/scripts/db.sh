#!/usr/bin/env bash

/usr/bin/php /var/www/html/artisan migrate --force
chown www-data:www-data /data/rental-notice.sqlite
