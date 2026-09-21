@echo off
cls
echo ========================================
echo   Accounting Module Migration Runner
echo ========================================
echo.

cd /d "c:\laragon\www\anix\ahpczportal"

echo Running migrations...
echo.

php artisan migrate --force

echo.
echo ========================================
echo Migration complete!
echo ========================================
echo.
echo Next steps:
echo 1. Import SQL seeder in phpMyAdmin
echo 2. Run: php artisan optimize:clear
echo 3. Check Accounting module in navigation
echo.
pause
