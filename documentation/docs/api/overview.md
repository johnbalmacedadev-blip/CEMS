# API Overview

CEMS exposes JSON endpoints primarily under the `/api/*` prefix. These power autocomplete fields, live chat, SOA, expenses, tools, and other dynamic UI features.

## Interactive documentation (Scribe)

The app includes **[Scribe](https://scribe.knuckles.wtf/laravel)** for auto-generated, interactive API docs.

### Generate docs

After installing dependencies:

```bash
php artisan scribe:generate
```

Re-run this command whenever routes or controller docblocks change.

### View docs

1. Start the app: `php artisan serve`
2. **Log in** to CEMS (session required for most endpoints)
3. Open **`/docs`** — e.g. [http://localhost:8000/docs](http://localhost:8000/docs)

Additional URLs:

| URL | Content |
|-----|---------|
| `/docs` | HTML API reference with **Try It Out** |
| `/docs.openapi` | OpenAPI 3 spec (YAML) |
| `/docs.postman` | Postman collection (JSON) |

## Authentication

CEMS API routes use **Laravel session authentication** (cookie), not Bearer tokens. You must be logged in via the web app before calling endpoints or using Try It Out in Scribe.

For Try It Out, Scribe is configured to use **CSRF protection** compatible with Laravel's web middleware.

## Endpoint groups

| Group | Prefix / routes | Purpose |
|-------|-----------------|---------|
| **Autocomplete** | `/api/makes/search`, `/api/models/search` | Make/model lookup |
| **Live Team Chat** | `/api/chat/*` | Messages, sync, presence |
| **Expenses** | `/api/expenses/*` | Vehicle search, categories, payment methods |
| **Tools** | `/api/tools/*` | Mechanic tools inventory CRUD |
| **SOA** | `/api/soa/*` | Statement of account operations |
| **Contracts** | `/api/contracts/vehicles/search` | Vehicle lookup for contracts |
| **Unit Report** | `vehicles/search-archiveable`, `POST vehicles/{id}/archive` | Archive workflow |

## Example: search archiveable vehicles

```bash
curl -X GET "http://localhost:8000/vehicles/search-archiveable?q=toyota" \
  -H "Accept: application/json" \
  -H "Cookie: laravel_session=YOUR_SESSION_COOKIE"
```

Response (abbreviated):

```json
[
  {
    "id": 12,
    "plate_number": "ABC 1234",
    "label": "2020 Toyota Vios (ABC 1234)",
    "status": "Available",
    "archive_url": "http://localhost:8000/vehicles/12/archive"
  }
]
```

## Example: archive a vehicle

```bash
curl -X POST "http://localhost:8000/vehicles/12/archive" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -H "Cookie: laravel_session=YOUR_SESSION_COOKIE"
```

Response:

```json
{
  "success": true,
  "message": "Vehicle moved to Archived successfully.",
  "swal_title": "Archived",
  "vehicle_id": 12,
  "vehicle": { "..." }
}
```

## Regenerating after code changes

Add to your deployment or dev workflow:

```bash
composer docs:api
```

This runs `php artisan scribe:generate` (defined in `composer.json`).
