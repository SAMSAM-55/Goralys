@echo off

setlocal EnableExtensions EnableDelayedExpansion

echo ==================================================
echo =====             Goralys setup              =====
echo ==================================================

echo Checking for Composer...
where composer>nul 2>&1
if errorlevel 1 (
    echo Fatal: Composer not found in PATH.
    echo Please install Composer or add it to your system PATH.
    pause
    exit /b
)

echo Installing dependencies ...
call composer install --working-dir=PHP
if errorlevel 1 (
    echo [ERROR] Composer install failed.
    pause
    exit /b
)
echo Successfully installed dependencies.
echo.

echo Creating .env file ...

if exist ".env" (
    echo "An existing .env file was found, do you want to overwrite it ? This will delete all previous configuration."
    set /p OVERWRITE="Overwrite ? (Y/n) :"
    if /I not "!OVERWRITE!" == "Y" (
        goto :done
    )
)

(
echo DATABASE_HOST="localhost"
echo DATABASE_ID="your db id (user)"
echo DATABASE_PASSWORD="your db password"
echo DATABASE_NAME="your db name"
echo MAIL_DOMAIN="your mail domain"
echo MAIL_USER="your mail user (address)"
echo MAIL_PASSWORD="your mail password"
) > .env

echo .env created successfully!

:done
echo ==================================================
echo =====             Setup Complete             =====
echo ==================================================
echo You can now edit your .env file and start coding