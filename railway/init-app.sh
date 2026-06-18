#!/usr/bin/env bash
set -e

php artisan migrate --force
php artisan db:seed --force
php artisan db:seed --class=CarbonData2026Seeder --force
