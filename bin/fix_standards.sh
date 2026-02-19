#!/bin/bash
# Description: Automatically fixes coding standards using PHPCBF inside the container.

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '#' | awk '/=/ {print $1}')
fi

echo "Running PHPCBF en $PHP_CONTAINER..."
docker exec -it $PHP_CONTAINER ./vendor/bin/phpcbf --tab-width=4 --encoding=utf-8 --standard=phpcs.xml application
