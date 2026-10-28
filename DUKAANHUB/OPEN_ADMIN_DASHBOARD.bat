@echo off
cd /d "%~dp0"
start http://localhost:8000/admin/index.php
call run.bat
