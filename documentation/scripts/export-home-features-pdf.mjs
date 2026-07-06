import { mkdirSync, writeFileSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';
import { mdToPdf } from 'md-to-pdf';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const exportDir = join(root, 'export');
const mdPath = join(exportDir, 'CEMS-Home-Features-Reference.md');
const pdfPath = join(exportDir, 'CEMS-Home-Features-Reference.pdf');

const markdown = `# CEMS Home Screen — Features & Sub-links Reference

*Generated: ${new Date().toLocaleString()}*

This document lists every Home category card, its sub-links, whether a page exists, and what each module contains.

**Legend:** ✅ Full page built · 🔀 Redirects to another module

---

## Also available (not a Home card)

| Feature | URL | Status | What's inside |
|---------|-----|--------|----------------|
| Login | /login | ✅ | Email/password login |
| Home | /home | ✅ | Category card grid + logout |
| Dashboard | /dashboard | ✅ | KPI cards, charts (vehicles, expenses, staff), recent units |
| Team Chat | Widget (all pages) | ✅ | Live messages, file/link attach, online users |

---

## 1. CAR REPORTS

*Unit reports, photos, pricelist*

| Sub-link | URL | Status | What's inside |
|----------|-----|--------|----------------|
| Car Photos Folder | /car-photos-folder | ✅ | Browse vehicle photos across inventory |
| Unit Report | /vehicles | ✅ | Full inventory list, status tabs, filters, export, vehicle detail (expenses, docs, images, status, prices) |
| Pricelist | /pricelist | ✅ | Posted/sold prices, financing per unit, bulk updates, PDF export |

---

## 2. STAFF REPORTS

*Trackers and recommendations*

| Sub-link | URL | Status | What's inside |
|----------|-----|--------|----------------|
| Buffing Tracker | /buffing-tracker | ✅ | CRUD — buffing jobs per vehicle/staff |
| Insurance Tracker | /insurance-tracker | ✅ | CRUD — insurance status per unit |
| Mechanic Tracker | /expenses-inventory?section=tools-purchase | 🔀 | Same as Expenses Report → tools purchase section |
| Driver Activity Tracker | /expenses-inventory?section=tools-purchase | 🔀 | Same page as Mechanic Tracker |
| Recommendation Tracker | /recommendation-tracker | ✅ | CRUD — recommendations with image uploads |
| Sales Agents | /staff-reports/sales-agents | ✅ | Staff report view for sales agents |
| Executive Agents | /staff-reports/executive-agents | ✅ | Executive agent records + add entries |

---

## 3. PAYMENTS/EXPENSES REPORTS

*Expenses, SOA, payroll, commission*

| Sub-link | URL | Status | What's inside |
|----------|-----|--------|----------------|
| Expenses Report | /expenses-inventory | ✅ | Expense transactions list, tools section, categories, CSV export |
| SOA Cash Vault | /soa/create | ✅ | Daily cash statement, starting cash, additions, manual entries, floated funds |
| Sales Agent Commission | /sales-agent-commissions | ✅ | CRUD — commission records per agent |
| Gas Expenses/P.O. Tracker | /gas-expense-po-tracker | ✅ | Purchase orders, gas expenses, PDF export |
| Payroll | /payroll | ✅ | Employee payroll reference list (filter by status, contract, search) |
| Source Screenshots | /source-screenshots | ✅ | CRUD — upload proof screenshots for transactions |

*Commission — removed (was duplicate of Sales Agent Commission)*

---

## 4. VLOGS AND POSTS REPORTS

*Video and posting trackers*

| Sub-link | URL | Status | What's inside |
|----------|-----|--------|----------------|
| Car Video Boost Report | /car-video-boost-report | ✅ | Manage vehicle ad/boost entries (add, edit, delete) |
| Video and Posting Tracker | /video-posting-tracker | ✅ | CRUD — video posting records per unit/campaign |

---

## 5. TRANSFERS/PAPERS REPORTS

*Documents and transfer OR/CR*

| Sub-link | URL | Status | What's inside |
|----------|-----|--------|----------------|
| Follow Up Documents | /follow-up-documents | ✅ | CRUD — pending document follow-ups after sale |
| Transfer ORCR | /transfer-orcr | ✅ | OR/CR transfer records, fees, summary report, PDF export |
| Vehicle Registration | /vehicle-registration | ✅ | CRUD — LTO registration tracking |

---

## 6. CUSTOMER LISTS

*Client and trail form lists*

| Sub-link | URL | Status | What's inside |
|----------|-----|--------|----------------|
| Client Follow Up List | /client-follow-up-list | ✅ | CRUD — client follow-ups, link to vehicle, multi-step follow-up fields |
| Appointment List | /appointment-list | ✅ | CRUD — scheduled client appointments |
| Trail Form List | /trail-form-list | ✅ | CRUD — client inquiries/reservations, inquiry source, vehicle type, sample data |

---

## 7. EQUIPMENT LISTS

*Mechanic tools and expenses*

| Sub-link | URL | Status | What's inside |
|----------|-----|--------|----------------|
| Mechanic Tools/Expenses | /mechanic-tools-expenses | 🔀 | Redirects to /expenses-inventory?section=tools-purchase — tool purchases & mechanic expenses |

---

## 8. COMPANY DOCUMENTS

*Employee list, contracts, memos, BOLO, settings*

| Sub-link | URL | Status | What's inside |
|----------|-----|--------|----------------|
| Employee List | /employees | ✅ | CRUD — HR employee records |
| Contracts | /contracts | ✅ | CRUD — sales contracts linked to vehicles |
| Memos | /memos | ✅ | Add memo text, upload file, or attach link; card list view |
| AR Form Templates | /document-templates | ✅ | CRUD — reusable document form templates |
| AR Template | /ar-template | ✅ | Upload file or add link for company AR template |
| Online AR BOLO | /online-ar-bolo | ✅ | Upload file or add link for online AR BOLO docs |
| Agent BOLO | /agent-bolo | ✅ | Per-agent profiles with attached files/links |

*Memorandums — removed*

---

## 9. ANALYTICS REPORT

*Financial and sales analytics pages*

| Sub-link | URL | Status | What's inside |
|----------|-----|--------|----------------|
| Financial Report | /analytics-report/financial | ✅ | Revenue, expenses, margins, status badges, export |
| Sales Report | /analytics-report/sales | ✅ | Sales performance metrics |
| Sales Executive Report | /analytics-report/sales-executive | ✅ | Executive-level sales breakdown |

---

## 10. SETTINGS

*System and application settings*

| Sub-link | URL | Status | What's inside |
|----------|-----|--------|----------------|
| Application Settings | /settings | ✅ | Hub with links to financing, branches, users, activity logs |
| User Activity Logs | /admin-docs | ✅ | Filterable audit log (login, page views, data changes) |

**Also inside Application Settings (admin):**

| Sub-link | URL | Status | What's inside |
|----------|-----|--------|----------------|
| Car Price List / Financing | /settings/financing | ✅ | Financing schemes for Pricelist |
| Branch / Location | /settings/branch-locations | ✅ | Branch master data CRUD |
| User Management | /settings/users | ✅ | Users CRUD + permissions matrix (admin only) |

---

## 11. COMPARE

*Compare listings across competitor websites*

| Sub-link | URL | Status | What's inside |
|----------|-----|--------|----------------|
| Compare Cars | /compare | ✅ | Search year/model/brand vs competitor listings, market comparison table |

---

## Summary

| Item | Value |
|------|-------|
| Home category cards | 11 |
| Total sub-links | 46 |
| All have working pages | Yes — none are empty placeholders |
| Shared/redirect links | Mechanic Tracker, Driver Activity Tracker, Mechanic Tools/Expenses (all → expenses tools section) |

**Recent changes:** Memos (/memos) and Trail Form List (/trail-form-list) added; Memorandums and duplicate Commission removed.
`;

mkdirSync(exportDir, { recursive: true });
writeFileSync(mdPath, markdown, 'utf8');

console.log('Generating PDF...');

await mdToPdf(
  { path: mdPath },
  {
    dest: pdfPath,
    pdf_options: {
      format: 'A4',
      margin: { top: '15mm', right: '12mm', bottom: '15mm', left: '12mm' },
      printBackground: true,
    },
    css: `
      body { font-family: Segoe UI, Arial, sans-serif; font-size: 9pt; line-height: 1.4; }
      h1 { font-size: 18pt; color: #1e40af; border-bottom: 2px solid #2563eb; padding-bottom: 6px; }
      h2 { font-size: 13pt; color: #1e3a8a; margin-top: 1.2em; page-break-after: avoid; }
      em { color: #64748b; }
      table { border-collapse: collapse; width: 100%; margin: 0.5em 0 1em; font-size: 8pt; }
      th, td { border: 1px solid #cbd5e1; padding: 4px 6px; text-align: left; vertical-align: top; }
      th { background: #f1f5f9; font-weight: 600; }
      tr:nth-child(even) td { background: #f8fafc; }
      hr { border: none; border-top: 1px solid #e2e8f0; margin: 1em 0; }
    `,
  },
);

console.log(`PDF created:\n  ${pdfPath}`);
