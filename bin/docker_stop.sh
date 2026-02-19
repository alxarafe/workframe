#!/bin/bash

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '#' | awk '/=/ {print $1}')
fi

echo "Deteniendo contenedores para $PROJECT_NAME..."
docker compose down
echo "Contenedores detenidos."
