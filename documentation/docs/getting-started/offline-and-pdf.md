---
sidebar_position: 4
---

# Offline & PDF export

You can use CEMS documentation **without internet** or save it as a **PDF**.

## Quick comparison

| Method | Best for | Flowcharts | Command |
|--------|----------|------------|---------|
| **Offline ZIP** | Browse full docs on USB / shared drive | ✅ Yes (with local server) | `npm run export:offline` |
| **PDF export** | Print, email, archive as one file | ⚠️ Tables & text (diagrams as code) | `npm run export:pdf` |
| **Browser Print** | Best-looking PDF with diagrams | ✅ Yes | Ctrl+P on any doc page |
| **Laravel copy** | Docs inside running app | ✅ Yes | `npm run build:laravel` |

---

## 1. Offline ZIP (recommended)

Creates a self-contained website you can copy anywhere.

```bash
cd documentation
npm run export:offline
```

**Output:** `documentation/export/CEMS-Documentation-Offline.zip`

### How to use the ZIP

1. Extract the ZIP to any folder
2. Start a simple local server inside the extracted folder:

```bash
python -m http.server 8080
```

3. Open **http://localhost:8080/docs/intro/** in your browser

:::tip
Flowcharts and navigation need a local server — opening `index.html` directly (`file://`) may not load assets correctly.
:::

---

## 2. PDF export (single file)

Generates one PDF with all documentation pages combined.

```bash
cd documentation
npm run export:pdf
```

**Output:** `documentation/export/CEMS-Documentation.pdf`

:::note
PDF export includes all text, tables, and headings. **Mermaid flowcharts** are shown as code blocks, not rendered diagrams. For PDFs with diagrams, use **Browser Print** (below).
:::

---

## 3. Browser Print to PDF (best diagrams)

For the highest-quality PDF **with flowcharts rendered**:

1. Build and open docs in the browser:

```bash
cd documentation
npm run build:laravel
php artisan serve
```

2. Open any page, e.g. http://127.0.0.1:8000/documentation/docs/system-features
3. Press **Ctrl+P** (or Cmd+P on Mac)
4. Choose **Save as PDF**
5. Enable **Background graphics** for best results

Repeat per section, or print longer pages like [System Features](./system-features).

---

## 4. Docs inside Laravel (online, same machine)

If Laravel is already running:

```bash
composer docs:build
php artisan serve
```

Visit: http://127.0.0.1:8000/documentation/docs/intro

The `public/documentation` folder is static HTML — copy it to another machine for offline use with a local server.

---

## From project root (shortcuts)

```bash
composer docs:build      # HTML into public/documentation
composer docs:export     # Offline ZIP
composer docs:pdf        # Single PDF file
```

---

## Export folder

All generated files are saved to:

```
documentation/export/
├── CEMS-Documentation-Offline.zip
├── CEMS-Documentation.pdf
├── CEMS-Documentation-combined.md   (source used for PDF)
└── README-OFFLINE.txt
```

This folder is git-ignored — regenerate anytime after doc updates.
