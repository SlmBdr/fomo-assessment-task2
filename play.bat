@echo off
set PHP_CMD=

where php >nul 2>nul
if %errorlevel% equ 0 set PHP_CMD=php
if not "%PHP_CMD%"=="" goto RUN

if exist "C:\xampp\php\php.exe" set PHP_CMD="C:\xampp\php\php.exe"
if not "%PHP_CMD%"=="" goto RUN

echo PHP tidak terdeteksi di PATH system Anda maupun folder default.
set /p USER_PHP="Silakan ketik lokasi file php.exe Anda (contoh: C:\xampp\php\php.exe): "
if not "%USER_PHP%"=="" set PHP_CMD="%USER_PHP%"

if "%PHP_CMD%"=="" (
    echo.
    echo Error: Lokasi PHP tidak ditentukan. Program dibatalkan.
    pause
    exit /b
)

:RUN
%PHP_CMD% "%~dp0play.php"
pause
