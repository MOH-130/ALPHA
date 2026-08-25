@echo off
title Alpha Gaming Shop - Local Web Server
echo ========================================================
echo   Alpha Gaming Shop - Digital Games ^& Subscriptions
echo ========================================================
echo.
echo Starting local web server...
echo Access Storefront: http://localhost:8000
echo Access Admin Panel: http://localhost:8000/admin/login
echo.
echo Demo Admin Account:
echo Email: admin@store.com
echo Pass:  password123
echo.
echo Demo Customer Account:
echo Email: customer@store.com
echo Pass:  password123
echo.
echo Press Ctrl+C to stop the server anytime.
echo ========================================================

set PHP_CMD=php
if exist "%~dp0php_bin\php.exe" (
    set "PHP_CMD=%~dp0php_bin\php.exe"
)

"%PHP_CMD%" -S localhost:8000 -t public public/index.php
pause
