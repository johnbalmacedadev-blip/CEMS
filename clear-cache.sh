#!/bin/bash
# Laravel Cache Clear Script for Production
# Run this on your live server via SSH or cPanel Terminal

echo "Clearing Laravel caches..."

# Clear route cache
php artisan route:clear
echo "✓ Route cache cleared"

# Clear config cache
php artisan config:clear
echo "✓ Config cache cleared"

# Clear application cache
php artisan cache:clear
echo "✓ Application cache cleared"

# Clear view cache
php artisan view:clear
echo "✓ View cache cleared"

# Clear compiled files
php artisan clear-compiled
echo "✓ Compiled files cleared"

echo ""
echo "All caches cleared successfully!"
echo ""
echo "If you want to re-optimize for production, run:"
echo "  php artisan config:cache"
echo "  php artisan route:cache"
echo "  php artisan view:cache"

