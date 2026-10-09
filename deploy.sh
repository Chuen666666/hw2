#!/bin/bash

set -e

# Writable
chmod 777 pdf

# Create shared .env
if [ ! -f .env ]; then
    cp example.env .env
    echo "Created .env from example.env"
    echo "Please edit .env before running the application."
fi

# Install PHP dependencies
composer install --no-dev --no-interaction

# Create Python venv
if [ ! -d bot/.venv ]; then
    python3 -m venv bot/.venv
    echo "Created bot/.venv"
fi

# Install Python dependencies
bot/.venv/bin/pip install -r bot/requirements.txt

echo "Deployment setup completed."