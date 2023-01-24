#!/bin/bash

if [ ! -f "vendor/autoload" ]; then
    composer install --no-progress --no-interaction
fi

if [ ! -f ".env" ]; then
    echo "Creating env file for env $APP_ENV"
    cp .env.example .env
else
    echo "env file exist"
fi

role=${CONTAINER_ROLE:-app}

if [ "$role" = "app" ]; then
    php artisan key:generate
    php artisan cache:clear
    php artisan config:clear
    php artisan migrate
    php artisan route:clear

    php artisan serve --port=$PORT --host=0.0.0.0 --env=.env
elif [ "$role" = "queue" ]; then
    echo "Running the queue"
    php/var/www/artisan queue:work --verbose --tries=3 --timeout=10
fi
