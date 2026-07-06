@echo off
echo ========================
echo CTRX: Installing PHP 
echo ========================
echo .
set "PHP_PATH=C:\xampp\php"

if not exist "%PHP_PATH%\php.exe" (
    echo [ERROR] PHP not found at: %PHP_PATH%
    echo .
    pause
    exit /b 1
)

powershell -NoProfile -ExecutionPolicy Bypass -Command "$p = [Environment]::GetEnvironmentVariable('Path', 'User'); if ($p -like '*%PHP_PATH%*') { exit 0 } else { exit 1 }"

if %errorlevel% equ 0 (
    echo PHP is already in PATH!
    echo .
    php -v 2>nul || echo Restart CMD to test PHP.
    echo .
    pause
    exit /b 0
) else (
    echo PHP not found in PATH. Adding it...
    echo Please wait...
    echo .
    echo ..
    echo ...
    echo ....
    echo .....
    echo .
)

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
"[Environment]::SetEnvironmentVariable('Path', [Environment]::GetEnvironmentVariable('Path', 'User') + ';%PHP_PATH%', 'User')"

echo PHP added to PATH from: %PHP_PATH%
echo .
php -v 2>nul || echo Congratulations
echo .
pause