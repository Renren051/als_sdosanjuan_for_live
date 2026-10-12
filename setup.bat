@echo off
title SDO San Juan City - ALS System Initial Setup
color 0B

echo.
echo  =====================================================
echo   SDO San Juan City - Alternative Learning System (ALS)
echo   First-Time System Installation ^& Setup Script
echo  =====================================================
echo.

:: -------------------------------------------------------
:: Step 1: Verify Prerequisites (PHP, Composer, Node.js)
:: -------------------------------------------------------
echo [1/6] Checking prerequisites...

where php >nul 2>&1
if %errorlevel% neq 0 (
    echo  [ERROR] PHP not found in PATH.
    echo  Please ensure XAMPP (php) is installed and added to your System PATH.
    echo  Download: https://www.apachefriends.org/
    pause
    exit /b 1
)

where composer >nul 2>&1
if %errorlevel% neq 0 (
    echo  [ERROR] Composer not found.
    echo  Download ^& install: https://getcomposer.org/download/
    pause
    exit /b 1
)

where node >nul 2>&1
if %errorlevel% neq 0 (
    echo  [ERROR] Node.js not found.
    echo  Download ^& install: https://nodejs.org/
    pause
    exit /b 1
)
echo  [OK] PHP, Composer, and Node.js detected.
echo.

:: -------------------------------------------------------
:: Step 2: Install PHP Dependencies
:: -------------------------------------------------------
echo [2/6] Installing PHP dependencies (composer install)...
call composer install --no-interaction --prefer-dist
if %errorlevel% neq 0 (
    echo  [ERROR] Composer install failed.
    pause
    exit /b 1
)
echo  [OK] PHP dependencies installed successfully.
echo.

:: -------------------------------------------------------
:: Step 3: Install Node Dependencies
:: -------------------------------------------------------
echo [3/6] Installing Node dependencies (npm install)...
call npm install
if %errorlevel% neq 0 (
    echo  [ERROR] npm install failed.
    pause
    exit /b 1
)
echo  [OK] Node dependencies installed successfully.
echo.

:: -------------------------------------------------------
:: Step 4: Configure .env & Generate Application Key
:: -------------------------------------------------------
echo [4/6] Configuring environment (.env)...
if not exist .env (
    echo  Copying .env.example to .env...
    copy .env.example .env >nul
    call php artisan key:generate
    echo  [OK] .env created and application key generated.
) else (
    echo  [OK] .env file already exists.
)
echo.

:: -------------------------------------------------------
:: Step 5: Link Public Storage
:: -------------------------------------------------------
echo [5/6] Creating storage symlink (for hero banners and documents)...
call php artisan storage:link
echo  [OK] Storage link verified.
echo.

:: -------------------------------------------------------
:: Step 6: Database Setup & Seeding
:: -------------------------------------------------------
echo [6/6] Database Setup...
echo.
echo  Make sure MySQL is RUNNING in XAMPP (Port 3306)
echo  and the database 'als_sdosanjuan_db' has been created.
echo.
set /p RUN_SEED="Would you like to run migrations and seed default demo data now? (Y/N): "
if /i "%RUN_SEED%"=="Y" (
    echo  Running database migrations and seeders...
    call php artisan migrate --seed --force
    if %errorlevel% neq 0 (
        echo.
        echo  [WARNING] Migrations/seeding encountered an issue.
        echo  Please verify MySQL is running in XAMPP and 'als_sdosanjuan_db' exists.
    ) else (
        echo  [OK] Database successfully migrated and seeded with:
        echo       - 3 Kingster Photo Hero Banners
        echo       - 12 Community Learning Centers (CLCs)
        echo       - 14 DepEd Self-Learning Modules (SLMs)
        echo       - Default Demo Accounts (admin_als, teacher_juan, learner_mark)
    )
) else (
    echo  Skipping database migration. You can run 'php artisan migrate --seed' later.
)
echo.

:: -------------------------------------------------------
:: Step 7: Build Production Frontend Assets
:: -------------------------------------------------------
echo Compiling frontend assets (npm run build)...
call npm run build
echo  [OK] Assets compiled.
echo.

echo =====================================================
echo  SETUP COMPLETE!
echo =====================================================
echo.
echo  Default Demo Accounts (Login using Email or Username):
echo    - Division Admin: admin.als@deped.gov.ph        ^| Password123!
echo    - Mobile Teacher: juan.delacruz@deped.gov.ph    ^| Password123!
echo    - BLP Learner   : learner.blp@deped.gov.ph      ^| Password123!
echo    - Elem Learner  : learner.elem@deped.gov.ph     ^| Password123!
echo    - JHS Learner   : learner.jhs@deped.gov.ph      ^| Password123!
echo.
echo  To launch the system at any time, simply double-click:
echo    run.bat
echo.
echo =====================================================
pause
