#!/bin/bash

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '#' | awk '/=/ {print $1}')
fi

echo "Ejecutando análisis estático en $PHP_CONTAINER..."
docker exec -it $PHP_CONTAINER ./vendor/bin/phpstan analyse -c phpstan.neon
