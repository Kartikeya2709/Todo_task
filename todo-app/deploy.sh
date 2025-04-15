#!/bin/bash

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! mysqladmin ping -h"$RAILWAY_DATABASE_HOST" -P"$RAILWAY_DATABASE_PORT" -u"$RAILWAY_DATABASE_USER" -p"$RAILWAY_DATABASE_PASSWORD" --silent; do
    echo "MySQL is not ready yet..."
    sleep 1
done

echo "MySQL is ready!"

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Start the server
echo "Starting the server..."
php artisan serve --port=$PORT 