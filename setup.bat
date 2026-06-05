@echo off
echo Setting up Car Empire Management System...
echo.

echo Installing Composer dependencies...
composer install

echo.
echo Generating application key...
php artisan key:generate

echo.
echo Running database migrations...
php artisan migrate

echo.
echo Seeding database with initial data...
php artisan db:seed

echo.
echo Creating storage directories...
if not exist "storage\app\public" mkdir "storage\app\public"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\logs" mkdir "storage\logs"

echo.
echo Setting permissions...
icacls "storage" /grant Everyone:F /T
icacls "bootstrap\cache" /grant Everyone:F /T

echo.
echo Setup completed successfully!
echo.
echo You can now start the development server with:
echo php artisan serve
echo.
echo Login credentials:
echo Admin: admin@carempire.com / admin123
echo User: john@carempire.com / john123
echo.
pause





















