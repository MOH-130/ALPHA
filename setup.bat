@echo off
title Alpha Gaming Shop Setup & Initializer
echo ========================================================
echo   Alpha Gaming Shop - Initializing Database & Seeders
echo ========================================================
echo.
echo Running setup...

set PHP_CMD=php
if exist "%~dp0php_bin\php.exe" (
    set "PHP_CMD=%~dp0php_bin\php.exe"
)

"%PHP_CMD%" -r "require_once 'bootstrap/app.php'; \App\Core\Database::initializeDatabase(); echo 'Database ready with demo records!';"
echo.
echo Setup Complete!
echo You can now run run.bat to start your store.
echo ========================================================
pause
