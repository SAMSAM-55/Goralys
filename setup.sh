echo "=================================================="
echo "=====             Goralys setup              ====="
echo "=================================================="

echo "Checking for Composer..."
if ! command -v composer >/dev/null 2>&1; then
    echo "Fatal: Composer not found in PATH."
    echo "Please install Composer or add it to your system PATH."
    exit 1
fi

echo "Installing dependencies ..."
composer install --working-dir=PHP
if [ $? -ne 0 ]; then
    echo "[ERROR] Composer install failed."
    exit 1
fi
echo "Successfully installed dependencies."
echo

echo "Creating .env file ..."

if [ -f .env ]; then
    echo "An existing .env file was found, do you want to overwrite it? This will delete all previous configuration."
    read -p "Overwrite ? (Y/n) : " OVERWRITE
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

cat > .env <<EOF
DATABASE_HOST="localhost"
DATABASE_ID="your db id (user)"
DATABASE_PASSWORD="your db password"
DATABASE_NAME="your db name"
MAIL_DOMAIN="your mail domain"
MAIL_USER="your mail user (address)"
MAIL_PASSWORD="your mail password"
EOF

echo ".env created successfully!"
echo
echo "=================================================="
echo "=====             Setup Complete             ====="
echo "=================================================="
echo "You can now edit your .env file and start coding"