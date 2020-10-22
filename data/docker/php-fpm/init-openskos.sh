#/bin/sh

cd /var/www/openskos
# init
composer install
php vendor/bin/phing config
