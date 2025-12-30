#!/usr/bin/env bash

echo "=================================================="
echo "=====             Goralys setup              ====="
echo "=================================================="

echo "Checking for Composer..."
if ! command -v composer >/dev/null 2>&1; then
    echo "Fatal: Composer not found in PATH."
    echo "Please install Composer or add it to your system PATH."
    exit 1
fi

if [ ! -d "./backend" ]; then
    echo "[ERROR] backend directory not found."
    exit 1
fi

echo "Installing dependencies ..."
composer install --working-dir=backend || {
    echo "[ERROR] Composer install failed."
    exit 1
}
echo "Successfully installed dependencies."
echo

echo "Creating .env file ..."

if [ -f ./backend/.env ]; then
    echo "An existing .env file was found, do you want to overwrite it?"
    printf "Overwrite ? (Y/n) : "
    read OVERWRITE
    if [[ "$OVERWRITE" != "Y" && "$OVERWRITE" != "y" ]]; then
        echo "Keeping the existing .env file."
        echo
        echo "=================================================="
        echo "=====             Setup Complete             ====="
        echo "=================================================="
        echo "You can now edit your .env file and start coding"
        exit 0
    fi
fi

cat > ./backend/.env <<EOF
DATABASE_HOST="localhost"
DATABASE_ID="your db id (user)"
DATABASE_PASSWORD="your db password"
DATABASE_NAME="your db name"
MAIL_DOMAIN="your mail domain"
MAIL_USER="your mail user (address)"
MAIL_PASSWORD="your mail password"
FOLDER="/"
PHP_SESSION_LIFETIME=3600
PHP_SESSION_LIFETIME_MULTIPLIER=1.25
GORALYS_ENVIRONMENT="dev"
EOF

echo ".env created successfully!"
echo
echo "=================================================="
echo "=====             Setup Complete             ====="
echo "=================================================="
echo "You can now edit your .env file and start coding"
