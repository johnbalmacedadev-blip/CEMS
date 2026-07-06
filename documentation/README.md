# CEMS Documentation (Docusaurus)

This folder contains the **Car Empire Management System** documentation site, built with [Docusaurus](https://docusaurus.io).

## Quick start

```bash
npm install
npm start
```

Opens at [http://localhost:3000](http://localhost:3000).

## Build for production

```bash
npm run build
npm run serve
```

## View docs with `php artisan serve` (recommended for staff)

Feature documentation is **not** at `/docs` — that URL is reserved for **Scribe API docs** inside Laravel.

Build and copy the static site into Laravel's `public/documentation` folder:

```bash
npm run build:laravel
```

Then from the project root:

```bash
cd ..
php artisan serve
```

| What | URL |
|------|-----|
| **Feature docs** (Docusaurus) | http://127.0.0.1:8000/documentation/docs/system-features |
| **API docs** (Scribe) | http://127.0.0.1:8000/docs *(login required)* |

Or run from the repo root: `composer docs:build`

## Live preview while editing (developers)

```bash
npm start
```

Opens at [http://localhost:3000](http://localhost:3000) — hot reload for writing docs.

## Deploy to GitHub Pages

From this folder:

```bash
GIT_USER=<your-github-username> npm run deploy
```

Or build and upload the `build/` folder to your hosting provider.

## Content structure

```
docs/
├── intro.md
├── getting-started/
│   ├── login.md
│   ├── installation.md
│   └── permissions.md
├── core/
│   ├── home.md
│   ├── dashboard.md
│   └── team-chat.md
├── modules/
│   ├── car-reports/       # Unit Report, Pricelist, Archiving
│   ├── staff-reports/
│   ├── payments-expenses/
│   ├── vlogs-posts/
│   ├── transfers-papers/
│   ├── customer-lists/
│   ├── equipment-lists/
│   ├── company-documents/
│   ├── analytics-report/
│   ├── settings/
│   └── compare/
└── api/
    └── overview.md
```

## API docs (Scribe)

Interactive API documentation is generated **inside the Laravel app**, not Docusaurus:

```bash
cd ..
composer docs:api
# Visit http://localhost:8000/docs while logged in
```

## Adding new module docs

1. Create `docs/modules/your-module.md`
2. Add a `_category_.json` if creating a new section
3. Run `npm start` to preview
