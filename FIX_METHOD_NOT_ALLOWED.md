# Fix: "Method Not Allowed" Error on Live Server

## The Problem
You're getting: **"The GET method is not supported for route /. Supported methods: HEAD."**

This happens because Laravel's route cache is stale or corrupted on your live server.

## Quick Fix (Run on Live Server)

### Via cPanel Terminal:
1. Open cPanel → Terminal
2. Navigate to your Laravel project:
   ```bash
   cd ~/public_html/car_empire
   # OR wherever your Laravel app is located
   ```
3. Run these commands:
   ```bash
   php artisan route:clear
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

### Via SSH:
If you have SSH access, connect and run the same commands above.

## After Clearing Cache

If you want to re-optimize for production (optional but recommended):
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Alternative: Delete Cache File Manually

If the commands don't work, you can manually delete the cache file:

1. Via cPanel File Manager or SSH, navigate to: `bootstrap/cache/`
2. Delete the file: `routes-v7.php` (or similar route cache file)
3. Refresh your website

## Verify It's Fixed

After clearing the cache, visit your website root URL. It should now:
- Redirect to `/login` if not authenticated
- Redirect to `/home` if authenticated

## Prevention

Always clear caches after deploying:
- After updating routes
- After updating config files
- After major deployments

## Still Having Issues?

1. Check file permissions:
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

2. Verify `.htaccess` is in the `public` directory

3. Check Laravel logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

