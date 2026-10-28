@echo off
cd /d "%~dp0"
title DukaanHub - Starting...
cls

echo.
echo  ██████╗ ██╗   ██╗██╗  ██╗ █████╗  █████╗ ███╗   ██╗██╗  ██╗██╗   ██╗██████╗
echo  ██╔══██╗██║   ██║██║ ██╔╝██╔══██╗██╔══██╗████╗  ██║██║  ██║██║   ██║██╔══██╗
echo  ██║  ██║██║   ██║█████╔╝ ███████║███████║██╔██╗ ██║███████║██║   ██║██████╔╝
echo  ██║  ██║██║   ██║██╔═██╗ ██╔══██║██╔══██║██║╚██╗██║██╔══██║██║   ██║██╔══██╗
echo  ██████╔╝╚██████╔╝██║  ██╗██║  ██║██║  ██║██║ ╚████║██║  ██║╚██████╔╝██████╔╝
echo  ╚═════╝  ╚═════╝ ╚═╝  ╚═╝╚═╝  ╚═╝╚═╝  ╚═╝╚═╝  ╚═══╝╚═╝  ╚═╝ ╚═════╝ ╚═════╝
echo.
echo  ============================================================
echo   EXCLUSIVE LUXURY E-COMMERCE STORE
echo   Email: Salmansahaneabdi@gmail.com
echo   Phone: +252 61 387 8223
echo  ============================================================
echo.

set "PHP_BIN=php"

where php >nul 2>nul
if %errorlevel% neq 0 (
    if exist "%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" (
        set "PHP_BIN=%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
    ) else if exist "C:\xampp\php\php.exe" (
        set "PHP_BIN=C:\xampp\php\php.exe"
    ) else (
        echo [ERROR] PHP not found. Please install PHP or XAMPP.
        pause
        exit /b
    )
)

echo  [OK] PHP Found. Starting DukaanHub at http://localhost:8000
echo  [OK] Opening browser...
echo.
echo  Press CTRL+C to stop the server.
echo.

start "" http://localhost:8000
"%PHP_BIN%" -S localhost:8000

pause
