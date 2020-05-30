#!/usr/bin/env bash
eval 'php artisan view:clear && php artisan cache:clear &&  php artisan config:cache && php artisan core:clear && php artisan migrate && php artisan db:seed --class=SyncSeeder'