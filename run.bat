@echo off
title ALS SDO San Juan - Dev Startup
color 0A

echo.
echo  =====================================================
echo   SDO San Juan City - ALS Portal
echo   Development Environment Launcher
echo  =====================================================
echo.

:: -------------------------------------------------------
:: Quick Sanity Check: Ensure initial setup was performed
:: -------------------------------------------------------
if not exist .env (
    echo  [WARNING] .env file not found.
    echo  It looks like this is your first time setting up the project!
    echo  Please run 'setup.bat' first to initialize the environment.
    echo.
    pause
    exit /b 1
)

if not exist vendor (
    echo  [WARNING] PHP vendor directory not found.
    echo  Please run 'setup.bat' first to install project dependencies.
    echo.
    pause
    exit /b 1
)

:: -------------------------------------------------------
:: Launch Servers
:: -------------------------------------------------------
echo  Starting local development servers...
echo.
echo  - Web Application: http://127.0.0.1:8000
echo  - Vite Dev Assets: http://localhost:5173
echo.
echo  Opening application in separate terminal windows.
echo  Close those windows at any time to stop the servers.
echo.

start "Laravel Server" cmd /k "cd /d %~dp0 && php artisan serve --host=127.0.0.1 --port=8000"
timeout /t 2 >nul
start "Vite Dev Server" cmd /k "cd /d %~dp0 && npm run dev"

echo.
echo  Servers launched successfully! Happy coding!
echo.
timeout /t 3 >nul
exit /b 0
