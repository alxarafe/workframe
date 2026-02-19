#!/bin/bash

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '#' | awk '/=/ {print $1}')
fi

echo "Iniciando contenedores para $PROJECT_NAME..."
docker compose up -d --build

echo "Contenedores iniciados. Acceda a http://localhost:$HTTP_PORT"
