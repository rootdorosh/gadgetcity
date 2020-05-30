#!/usr/bin/env bash
eval 'composer dump-autoload && php artisan view:clear && php artisan cache:clear &&  php artisan config:cache'