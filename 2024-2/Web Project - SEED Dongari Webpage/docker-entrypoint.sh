#!/bin/bash
set -e

cd /var/www/html
composer install --no-dev --optimize-autoloader
apache2-foreground