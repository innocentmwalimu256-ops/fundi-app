@echo off
setlocal enabledelayedexpansion
title FUNDI - Fresh Reset & Seed Runner (MySQL / XAMPP)
color 0B

:: 1. Add XAMPP PHP to Path
where php >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    if exist "C:\xampp\php\php.exe" (
        set "PATH=%PATH%;C:\xampp\php"
    ) else if exist "D:\xampp\php\php.exe" (
        set "PATH=%PATH%;D:\xampp\php"
    )
)

cd /d "%~dp0"

echo ======================================================================
echo           FUNDI - Reset Database & Fresh Seed (MySQL)
echo ======================================================================
echo.

:: 2. Check / Create MySQL Database
echo [1/4] Ensuring database exists on MySQL...
php create_mysql_db.php
if %ERRORLEVEL% NEQ 0 (
    color 0C
    echo.
    echo [ERROR] MySQL is not running! Tafadhali washa MySQL kwenye XAMPP kwanza.
    echo.
    pause
    exit /b
)

echo [2/4] Running fresh migrations and seeding MySQL database...
call php artisan migrate:fresh --seed --force

echo [3/4] Linking public storage directory...
call php artisan storage:link >nul 2>&1

echo [4/4] Opening browser to FUNDI Login page...
start http://127.0.0.1:8000

echo.
echo ======================================================================
echo FUNDI is now running with MySQL at http://127.0.0.1:8000
echo ======================================================================
echo.
php artisan serve --port=8000
pause
