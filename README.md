# Car Empire Management System (CEMS)

Laravel 10 application for car dealership operations — inventory, sales, finance, payroll, contracts, analytics, and team collaboration.

## Documentation

| Type | Location | How to view |
|------|----------|-------------|
| **User & developer guides** | [`documentation/`](documentation/) (Docusaurus) | `cd documentation && npm install && npm start` → http://localhost:3000 |
| **API reference** | Scribe (generated) | `composer docs:api` then http://localhost:8000/docs (login required) |

Start with the [documentation intro](documentation/docs/intro.md) or the [installation guide](documentation/docs/getting-started/installation.md).

## Quick start (application)

```bash
composer install
cp .env.example .env   # Windows: copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve
```

Open http://localhost:8000 and log in.

## Technology stack

- **Backend:** Laravel 10, PHP 8.1+
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap 5, Blade, SweetAlert2
- **PDF:** DomPDF
- **Docs:** Docusaurus + Scribe

## Repository

https://github.com/johnbalmacedadev-blip/CEMS

## License

MIT
