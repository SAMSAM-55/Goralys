@echo off
REM =====================================================
REM Deploy script: Clean and copy project to local XAMPP folder
REM Target: C:\xampp\htdocs\goralys
REM =====================================================

echo Deploying project to C:\xampp\htdocs\goralys ...

REM Create target directory if it doesn't exist
if not exist "C:\xampp\htdocs\goralys" (
    mkdir "C:\xampp\htdocs\goralys"
)

REM Delete all files and subdirectories in the target folder
echo Cleaning target directory...
rmdir /S /Q "C:\xampp\htdocs\goralys"
mkdir "C:\xampp\htdocs\goralys"

REM Copy all files and folders recursively, overwrite existing files
echo Copying new files...
xcopy * "C:\xampp\htdocs\goralys" /E /H /C /I /Y

echo.
echo Deployment complete!
pause
