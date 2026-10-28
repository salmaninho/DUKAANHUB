@echo off
cd /d "%~dp0"
where code >nul 2>nul
if %errorlevel% equ 0 (
    code .
) else if exist "%LOCALAPPDATA%\Programs\Microsoft VS Code\Code.exe" (
    "%LOCALAPPDATA%\Programs\Microsoft VS Code\Code.exe" .
) else if exist "%ProgramFiles%\Microsoft VS Code\Code.exe" (
    "%ProgramFiles%\Microsoft VS Code\Code.exe" .
) else (
    echo VS Code not found in default paths. Opening project folder in Explorer...
    explorer .
)
