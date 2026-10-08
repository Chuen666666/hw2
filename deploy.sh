#!/bin/bash

set -e

# Create a writable folder for PDFs
chmod 777 pdf

# Create config.php
if [ ! -f config.php ]; then
    cp config.example.php config.php
    echo "Created config.php from config.example.php"
fi

# Create Bot .env
if [ ! -f bot/.env ]; then
    cp bot/example.env bot/.env
    echo "Created bot/.env from bot/example.env"
fi

# Create Python venv
if [ ! -d bot/.venv ]; then
    python3 -m venv bot/.venv
    echo "Created bot/.venv"
fi

# Install requirements.txt
bot/.venv/bin/pip install -r bot/requirements.txt

echo "Deployment setup completed."