@echo off
echo Installing dependencies...
composer install

echo Copying .env file...
copy .env.example .env

echo Generating APP_KEY...
php artisan key:generate

echo.
echo ===================================
echo Setup completed!
echo ===================================
echo.
echo Next steps:
echo 1. Configure database name in .env (if different from waroeng_smart_db)
echo 2. Run: php artisan serve
echo.
pause