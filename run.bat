@echo off
setlocal
title FUNDI - Web-Based Technician Marketplace (MySQL / XAMPP)
color 0A

:: 1. Add XAMPP PHP to Path if not already present
where php >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    if exist "C:\xampp\php\php.exe" (
        set "PATH=%PATH%;C:\xampp\php"
    ) else if exist "D:\xampp\php\php.exe" (
        set "PATH=%PATH%;D:\xampp\php"
    )
)

echo ======================================================================
echo           FUNDI - Find. Connect. Fix. (MySQL Engine)
echo ======================================================================
echo.

:: 2. Change directory to project root
cd /d "%~dp0"

:: 3. Check / Create MySQL Database
echo [1/3] Checking MySQL database connection on XAMPP...
php create_mysql_db.php
if %ERRORLEVEL% NEQ 0 (
    color 0C
    echo.
    echo ======================================================================
    echo  [TAARIFA MUHIMU]
    echo  Tafadhali washa MySQL kwenye XAMPP Control Panel kisha ufungue tena!
    echo ======================================================================
    echo.
    pause
    exit /b 1
)

:: 4. Ensure storage link
call php artisan storage:link >nul 2>&1

:: 5. Open browser
echo [2/3] Opening FUNDI in your default browser...
start http://127.0.0.1:8000

:: 6. Launch Laravel Server
echo [3/3] Starting server at http://127.0.0.1:8000 ...
echo.
echo ======================================================================
echo   DATABASE: MySQL (fundi_db on 127.0.0.1:3306)
echo   DEFAULT LOGINS:
echo   - Super Admin : admin@fundi.co.tz       / password
echo   - Client      : client@fundi.co.tz      / password
echo   - Technician  : fundi.umeme@fundi.co.tz / password
echo ======================================================================
echo.
echo Server is running. Keep this window open while using the app.
echo.

php artisan serve --port=8000
pause
