@echo off
title ALS SDO San Juan - Dev Startup
color 0A

echo.
echo  =====================================================
echo   SDO San Juan City - ALS Portal
echo   Development Environment Startup Script
echo  =====================================================
echo.

:: -------------------------------------------------------
:: Step 1: Check if Composer is available
:: -------------------------------------------------------
echo [1/5] Checking Composer...
where composer >nul 2>&1
if %errorlevel% neq 0 (
    echo  [ERROR] Composer not found. Please install Composer first.
    echo  Download: https://getcomposer.org/download/
    pause
    exit /b 1
)
echo  [OK] Composer found.
echo.

:: -------------------------------------------------------
:: Step 2: Install PHP dependencies
:: -------------------------------------------------------
echo [2/5] Installing PHP dependencies (composer install)...
call composer install --no-interaction --prefer-dist
if %errorlevel% neq 0 (
    echo  [ERROR] Composer install failed.
    pause
    exit /b 1
)
echo  [OK] PHP dependencies installed.
echo.

:: -------------------------------------------------------
:: Step 3: Install Node dependencies & Security Audit
:: -------------------------------------------------------
echo [3/5] Installing Node dependencies (npm install)...
call npm install
if %errorlevel% neq 0 (
    echo  [ERROR] npm install failed. Make sure Node.js is installed.
    echo  Download: https://nodejs.org/
    pause
    exit /b 1
)
echo  [OK] Node dependencies installed.

echo  Running automated security check (npm audit fix)...
call npm audit fix
echo  [OK] Node dependencies audited and secured.
echo.

:: -------------------------------------------------------
:: Step 4: Set up .env if it does not exist
:: -------------------------------------------------------
echo [4/5] Checking .env file...
if not exist .env (
    echo  .env not found. Copying from .env.example...
    copy .env.example .env >nul
    call php artisan key:generate
    echo  [OK] .env created and app key generated.
) else (
    echo  [OK] .env already exists.
)
echo.

:: -------------------------------------------------------
:: Step 5: Run database migrations
:: -------------------------------------------------------
echo [5/5] Running database migrations...
call php artisan migrate --force
if %errorlevel% neq 0 (
    echo  [WARNING] Migration had issues. Check your DB connection in .env.
) else (
    echo  [OK] Migrations complete.
)
echo.

:: -------------------------------------------------------
:: Launch Servers
:: -------------------------------------------------------
echo  =====================================================
echo   Starting Development Servers...
echo  =====================================================
echo.
echo  - Laravel  : http://127.0.0.1:8000
echo  - Vite HMR : http://localhost:5173
echo.
echo  Two terminal windows will open.
echo  Close them to stop the servers.
echo.
pause

start "Laravel Server" cmd /k "cd /d %~dp0 && php artisan serve"
timeout /t 2 >nul
start "Vite Dev Server" cmd /k "cd /d %~dp0 && npm run dev"

echo.
echo  Both servers are now running. Happy coding!
echo.
pause
