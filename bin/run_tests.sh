#!/bin/bash

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '#' | awk '/=/ {print $1}')
fi

echo "Ejecutando tests en $PHP_CONTAINER..."
docker exec -it $PHP_CONTAINER ./vendor/bin/phpunit "$@"
