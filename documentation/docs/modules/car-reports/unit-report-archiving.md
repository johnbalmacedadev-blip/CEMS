---
sidebar_position: 4
---

# Archiving units

Archiving moves older units out of active inventory while **keeping all data** (expenses, documents, history). Archived units are only visible on the **Archived** tab.

## Who can archive?

Users with **`vehicles` → update** permission.

## Which units can be archived?

| Status | Can archive? |
|--------|--------------|
| Available | ✅ Yes |
| Released | ✅ Yes |
| Forfeited | ✅ Yes |
| Reserved | ❌ No |
| Under Maintenance | ❌ No |
| Already Archived | ❌ No |

Units with forfeit details but a different status are treated as archiveable when they qualify as forfeited.

## How to archive — from status tabs

1. Open **Unit Report**
2. Go to **Available**, **Released**, or **Forfeited**
3. Find the unit in the table
4. Click **Archive** on the row
5. Confirm in the dialog

The row is removed immediately and a success notification appears — no manual refresh needed.

## How to archive — from vehicle detail

1. Open the unit's **View Details** page
2. Click **Archive This Unit** in the toolbar
3. Confirm in the dialog

You are redirected to the **Archived** tab after success.

## How to archive — from the Archived tab

Use this when adding units to an empty archived list or searching by plate:

1. Open **Unit Report** → **Archived** tab
2. Click **Add to Archive**
3. Search by plate, make, or model
4. Click **Archive** on the matching result
5. Confirm in the dialog

The unit appears in the archived table immediately.

## What happens when a unit is archived?

| Field | Change |
|-------|--------|
| `status` | Set to `Archived` |
| `archived_at` | Timestamp of when it was archived |
| `status_before_archive` | Previous status (for reference) |
| `sale_status` (if set) | Updated to `Archived` |

The unit **does not appear** in:

- Available, Reserved, Released, or Forfeited tabs
- All Units tab
- Pricelist active listings

## Viewing archived units

1. **Unit Report** → **Archived** tab
2. Filter or search as needed
3. Click **View Details** to open the full record (read-only for status changes)

Archived rows show the archive date under the status badge.

## API endpoints

See the [API Reference](../../api/overview) for:

- `GET /vehicles/search-archiveable` — search units that can be archived
- `POST /vehicles/{vehicle}/archive` — archive a unit (JSON or redirect)

Interactive docs: `{APP_URL}/docs`
