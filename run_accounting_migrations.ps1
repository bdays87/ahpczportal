# Accounting Module Migrations Script
# Run this in PowerShell or right-click > Run with PowerShell

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Accounting Module Migration Runner" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Change to project directory
Set-Location "c:\laragon\www\anix\ahpczportal"

Write-Host "Running all accounting migrations..." -ForegroundColor Yellow
Write-Host ""

# Run migrate command to execute all pending migrations
php artisan migrate --force

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "Migration Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Import SQL seeder in phpMyAdmin: database/seeders/accounting_module_seeder.sql" -ForegroundColor White
Write-Host "2. Run: php artisan optimize:clear" -ForegroundColor White
Write-Host "3. Log in and check Accounting module in navigation" -ForegroundColor White
Write-Host ""

# Pause to see results
Read-Host "Press Enter to close"
