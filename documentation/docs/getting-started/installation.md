# Installation

This guide covers running CEMS on **Windows with XAMPP**, which matches the current development setup.

## Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.1 or higher |
| Composer | 2.x |
| MySQL / MariaDB | 5.7+ / 10.3+ |
| Node.js | 18+ (for documentation site only) |
| Extensions | `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` or `imagick` |

## 1. Clone the repository

```bash
git clone https://github.com/johnbalmacedadev-blip/CEMS.git
cd CEMS
```

Or place the project under `C:\xampp\htdocs\` and use Apache instead of `artisan serve`.

## 2. Install PHP dependencies

```bash
composer install
```

## 3. Environment configuration

Copy the environment file and generate an application key:

```bash
copy .env.example .env
php artisan key:generate
```

Edit `.env` with your database settings:

```env
APP_NAME="Car Empire Management System"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cems_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

Create the database in phpMyAdmin before running migrations.

## 4. Database setup

```bash
php artisan migrate
php artisan db:seed
```

## 5. Storage link

Vehicle images and uploads are stored on the public disk:

```bash
php artisan storage:link
```

## 6. Run the application

**Option A — Artisan (recommended for development)**

```bash
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000)

**Option B — XAMPP Apache**

Point your virtual host or access the project via `http://localhost/CarEmpire/.../public`.

## 7. Default login

After seeding, use the credentials defined in your database seeder. Contact your system administrator if you do not have login details.

:::tip
Admin users bypass page-level permission checks. Regular users need explicit permissions assigned under **Settings → Users**.
:::

## 8. Generate API documentation (optional)

Scribe generates interactive API docs served by the Laravel app:

```bash
php artisan scribe:generate
```

Then visit [http://localhost:8000/docs](http://localhost:8000/docs) while logged in.

## 9. Run the documentation site (optional)

User and developer guides are in the `documentation/` folder:

```bash
cd documentation
npm install
npm start
```

Opens at [http://localhost:3000](http://localhost:3000).

## Troubleshooting

| Issue | Fix |
|-------|-----|
| 500 error after install | Check `.env` DB credentials; run `php artisan config:clear` |
| Images not showing | Run `php artisan storage:link` |
| Permission denied on storage | Ensure `storage/` and `bootstrap/cache/` are writable |
| Composer memory limit | Run `php -d memory_limit=-1 $(which composer) install` |
