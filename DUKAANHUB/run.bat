@echo off
cd /d "%~dp0"
title DukaanHub - Luxury E-Commerce Platform
cls

echo =====================================================================
echo                DUKAANHUB - PREMIUM E-COMMERCE PLATFORM
echo =====================================================================
echo.
echo  * High-Contrast White Mode and Obsidian Dark Mode
echo  * Fast 1-Click Popup Login & 6-Digit Password Reset Verification
echo  * 31+ Luxury Products Across 6 Handcrafted Categories
echo.
echo  * STORE CONTACT:
echo    ---------------------------------------------------------------
echo    ADMINISTRATOR: Salman Sahane Abdi
echo    EMAIL:         Salmansahaneabdi@gmail.com
echo    PHONE:         252613878223 (+252 61 387 8223)
echo    ---------------------------------------------------------------
echo.
echo =====================================================================
echo  Detecting PHP Runtime Environment...
echo =====================================================================

set "PHP_BIN=php"

where php >nul 2>nul
if %errorlevel% neq 0 (
    if exist "%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" (
        set "PHP_BIN=%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
    ) else if exist "C:\xampp\php\php.exe" (
        set "PHP_BIN=C:\xampp\php\php.exe"
    ) else (
        echo [!] PHP was not found in PATH. Please install PHP or XAMPP.
        pause
        exit /b
    )
)

echo [OK] Using PHP: %PHP_BIN%
echo.
echo  Starting DukaanHub Server at: http://localhost:8000
echo  Opening in your default browser...
echo.

start http://localhost:8000
"%PHP_BIN%" -S localhost:8000

pause
