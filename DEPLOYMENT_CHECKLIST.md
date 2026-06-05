# Laravel Deployment Checklist for cPanel

## Critical Steps to Fix the "Method Not Allowed" Error

### 1. ✅ `.htaccess` File
The `.htaccess` file has been created in the `public` directory. Make sure it's uploaded to your live server.

### 2. Document Root Configuration
**IMPORTANT:** In cPanel, ensure your document root is set to the `public` directory:
- Go to cPanel → Domains → Your Domain → Document Root
- Set it to: `public_html/public` (or `public_html/your-subdirectory/public`)

**OR** if you can't change the document root, you'll need to:
- Upload all files to `public_html`
- Move the contents of the `public` folder to `public_html`
- Update paths in `public/index.php` to point to the parent directory

### 3. Clear Route Cache (Run on Live Server)
After uploading files, SSH into your server or use cPanel Terminal and run:
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 4. File Permissions
Set proper permissions on the live server:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 5. Environment Configuration
- Create a `.env` file on the live server with your production settings
- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Configure your database credentials
- Generate a new `APP_KEY` if needed: `php artisan key:generate`

### 6. Storage Link
Create a symbolic link for storage:
```bash
php artisan storage:link
```

### 7. Composer Dependencies
Make sure to run on the live server:
```bash
composer install --optimize-autoloader --no-dev
```

### 8. Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Quick Fix Commands (Run on Live Server)
If you're still getting the error, run these commands in order:

```bash
# Clear all caches
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Re-optimize
php artisan config:cache
php artisan route:cache
```

## Common Issues

### Issue: "Method Not Allowed" Error
**Solution:** 
1. Ensure `.htaccess` is in the `public` directory
2. Clear route cache: `php artisan route:clear`
3. Verify document root points to `public` directory

### Issue: 500 Internal Server Error
**Solution:**
1. Check file permissions (storage and bootstrap/cache should be writable)
2. Check `.env` file exists and has correct settings
3. Check Laravel logs: `storage/logs/laravel.log`

### Issue: Assets Not Loading
**Solution:**
1. Run `php artisan storage:link`
2. Check that `public/storage` symlink exists
3. Verify file permissions



