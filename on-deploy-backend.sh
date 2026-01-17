#!/usr/bin/env bash

set -euo pipefail

# Custom script to delete anything non-essential to the backend and set it up
# This script is used when deploying the backend and the frontend on different servers

find . -maxdepth 1 ! -name '.' ! -name 'LICENSE' ! -name 'README.md' ! -name 'CONTRIBUTING.md' ! -name 'backend' ! -name 'on-deploy-backend.sh' -exec rm -rf {} +

# Setup the backend

echo "=================================================="
echo "=====         Goralys backend setup          ====="
echo "=================================================="

echo "Checking for Composer..."
if ! command -v composer >/dev/null 2>&1; then
    echo "Fatal: Composer not found in PATH."
    echo "Please install Composer or add it to your system PATH."
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

if [ -f "./backend/.env" ]; then
    echo "An existing .env file was found, do you want to overwrite it ?"
    read -r -p "Overwrite ? (Y/n) : " OVERWRITE
    if [[ "${OVERWRITE:-Y}" != "Y" ]]; then
        echo "Keeping existing .env"
        goto_after_env=true
    fi
fi

if [ "${goto_after_env:-false}" != "true" ]; then
cat > ./backend/.env <<'EOF'
DATABASE_HOST="localhost"
DATABASE_ID="your db id (user)"
DATABASE_PASSWORD="your db password"
DATABASE_NAME="your db name"
FOLDER="/"
PHP_SESSION_LIFETIME=3600
PHP_SESSION_LIFETIME_MULTIPLIER=1.25
GORALYS_ENVIRONMENT="dev"
EOF

    echo ".env ready."
    echo
fi

echo "Creating Logs directory ..."
mkdir -p ./backend/Logs
echo "Creating Assets directory ..."
mkdir -p ./backend/Assets
echo "Directories are ready."
echo

read -r -p "Would you like the setup to run checks (phpcs)? (Y/n) : " RUN_CHECKS
if [[ "${RUN_CHECKS:-Y}" != "Y" ]]; then
    goto_done=true
fi

if [ "${goto_done:-false}" != "true" ]; then
    echo
    echo "Running phpcs checks..."

    if ! php backend/vendor/bin/phpcs --standard=PSR12 --ignore=vendor/* backend; then
        echo
        echo "PHPCS found coding standard violations."
        read -r -p "Run phpcbf to auto-fix what it can ? (Y/n) : " RUN_FIX

        if [[ "${RUN_FIX:-Y}" == "Y" ]]; then
            php backend/vendor/bin/phpcbf --standard=PSR12 --ignore=vendor/* backend || {
                echo "[ERROR] PHPCBF failed."
                exit 1
            }

            echo "Re-running phpcs to verify..."
            php backend/vendor/bin/phpcs --standard=PSR12 --ignore=vendor/* backend || {
                echo "[ERROR] Some PHPCS issues remain after PHPCBF."
                echo "You will need to fix the remaining violations manually."
                exit 1
            }

            echo "PHPCS clean after PHPCBF."
        else
            echo "Skipped PHPCBF. You can run it later."
            exit 1
        fi
    else
        echo "PHPCS clean."
    fi
fi

echo
echo "=================================================="
echo "=====             Setup Complete             ====="
echo "=================================================="