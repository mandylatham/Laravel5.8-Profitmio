#!/bin/bash

# Install new composer packages
composer install --no-dev --prefer-dist

# Cache boost configuration and routes
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# Sync database changes
php artisan migrate

# Install new node modules
npm install

# Build assets when using Laravel Mix
npm run dev

# Rise from the ashes
php artisan up

echo 'Deploy finished.'
