# Permissions & roles

CEMS uses **role-based access** combined with **per-page permissions** for non-admin users.

## User roles

| Role | Access |
|------|--------|
| **Admin** | Full access to all pages and actions; user management |
| **User** | Access only to pages explicitly granted in their permission profile |

Admins are managed under **Settings → Users** and are not restricted by `@canPage` checks in the UI.

## Page permissions

Each module maps to a **page key** (e.g. `vehicles`, `payroll`, `expenses`). For each page, a user may have:

| Action | Meaning |
|--------|---------|
| **view** | See the page and read data |
| **create** | Add new records |
| **update** | Edit existing records |
| **delete** | Remove records |

In Blade templates, permissions are checked with:

```php
@canPage('vehicles', 'update')
    {{-- Show edit/archive buttons --}}
@endcanPage
```

## Assigning permissions

1. Log in as an **admin**
2. Go to **Settings → Users**
3. Select a user → **Permissions**
4. Enable the pages and actions they need
5. Save

Changes take effect on the user's next request (no re-login required).

## Common page keys

| Page key | Module |
|----------|--------|
| `vehicles` | Unit Report, vehicle detail, pricelist |
| `expenses` | Expenses inventory |
| `payroll` | Payroll |
| `contracts` | Contracts |
| `transfer-orcr` | Transfer OR/CR |
| `analytics` | Analytics dashboard |
| `settings` | System settings (non-user) |

:::note
The exact list of page keys is defined in the user permissions UI. When documenting a new module, register its page key in the permission system.
:::

## API access

Most JSON endpoints under `/api/*` require an **authenticated session** (cookie-based login). You must be logged in to the web app before using **Try It Out** in Scribe docs at `/docs`.
