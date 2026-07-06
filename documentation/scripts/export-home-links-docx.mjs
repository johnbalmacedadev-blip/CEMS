import { Document, HeadingLevel, Packer, Paragraph, Table, TableCell, TableRow, TextRun, WidthType } from 'docx';
import { mkdirSync, writeFileSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const exportDir = join(root, 'export');
const outPath = join(exportDir, 'CEMS-Home-Module-Links.docx');

const sections = [
  {
    num: 1,
    title: 'CAR REPORTS',
    description: 'Unit reports, photos, pricelist',
    items: [
      ['Car Photos Folder', '/car-photos-folder'],
      ['Unit Report', '/vehicles'],
      ['Pricelist', '/pricelist'],
    ],
  },
  {
    num: 2,
    title: 'STAFF REPORTS',
    description: 'Trackers and recommendations',
    items: [
      ['Buffing Tracker', '/buffing-tracker'],
      ['Insurance Tracker', '/insurance-tracker'],
      ['Mechanic Tracker', '/expenses-inventory?section=tools-purchase'],
      ['Driver Activity Tracker', '/expenses-inventory?section=tools-purchase'],
      ['Recommendation Tracker', '/recommendation-tracker'],
      ['Sales Agents', '/staff-reports/sales-agents'],
      ['Executive Agents', '/staff-reports/executive-agents'],
    ],
  },
  {
    num: 3,
    title: 'PAYMENTS/EXPENSES REPORTS',
    description: 'Expenses, SOA, payroll, commission',
    items: [
      ['Expenses Report', '/expenses-inventory'],
      ['SOA Cash Vault', '/soa/create'],
      ['Sales Agent Commission', '/sales-agent-commissions'],
      ['Gas Expenses/P.O. Tracker', '/gas-expense-po-tracker'],
      ['Payroll', '/payroll'],
      ['Source Screenshots', '/source-screenshots'],
    ],
    note: 'Commission — removed (duplicate of Sales Agent Commission)',
  },
  {
    num: 4,
    title: 'VLOGS AND POSTS REPORTS',
    description: 'Video and posting trackers',
    items: [
      ['Car Video Boost Report', '/car-video-boost-report'],
      ['Video and Posting Tracker', '/video-posting-tracker'],
    ],
  },
  {
    num: 5,
    title: 'TRANSFERS/PAPERS REPORTS',
    description: 'Documents and transfer OR/CR',
    items: [
      ['Follow Up Documents', '/follow-up-documents'],
      ['Transfer ORCR', '/transfer-orcr'],
      ['Vehicle Registration', '/vehicle-registration'],
    ],
  },
  {
    num: 6,
    title: 'CUSTOMER LISTS',
    description: 'Client and trail form lists',
    items: [
      ['Client Follow Up List', '/client-follow-up-list'],
      ['Appointment List', '/appointment-list'],
      ['Trail Form List', '/trail-form-list'],
    ],
  },
  {
    num: 7,
    title: 'EQUIPMENT LISTS',
    description: 'Mechanic tools and expenses',
    items: [
      ['Mechanic Tools/Expenses', '/mechanic-tools-expenses → /expenses-inventory?section=tools-purchase'],
    ],
  },
  {
    num: 8,
    title: 'COMPANY DOCUMENTS',
    description: 'Employee list, contracts, memos, BOLO, settings',
    items: [
      ['Employee List', '/employees'],
      ['Contracts', '/contracts'],
      ['Memos', '/memos'],
      ['AR Form Templates', '/document-templates'],
      ['AR Template', '/ar-template'],
      ['Online AR BOLO', '/online-ar-bolo'],
      ['Agent BOLO', '/agent-bolo'],
    ],
    note: 'Memorandums — removed',
  },
  {
    num: 9,
    title: 'ANALYTICS REPORT',
    description: 'Financial and sales analytics pages',
    items: [
      ['Financial Report', '/analytics-report/financial'],
      ['Sales Report', '/analytics-report/sales'],
      ['Sales Executive Report', '/analytics-report/sales-executive'],
    ],
  },
  {
    num: 10,
    title: 'SETTINGS',
    description: 'System and application settings',
    items: [
      ['Application Settings', '/settings'],
      ['User Activity Logs', '/admin-docs'],
    ],
    extra: [
      'Also reachable from Application Settings (admin):',
      'Car Price List / Financing → /settings/financing',
      'Branch / Location → /settings/branch-locations',
      'User Management → /settings/users',
    ],
  },
  {
    num: 11,
    title: 'COMPARE',
    description: 'Compare listings across competitor websites',
    items: [['Compare Cars', '/compare']],
  },
];

function cell(text, bold = false) {
  return new TableCell({
    children: [new Paragraph({ children: [new TextRun({ text, bold, size: 22 })] })],
  });
}

function linkTable(items) {
  return new Table({
    width: { size: 100, type: WidthType.PERCENTAGE },
    columnWidths: [3500, 5500],
    rows: [
      new TableRow({
        children: [cell('Sub-link', true), cell('URL', true)],
      }),
      ...items.map(([label, path]) => new TableRow({ children: [cell(label), cell(path)] })),
    ],
  });
}

const children = [
  new Paragraph({
    heading: HeadingLevel.TITLE,
    children: [new TextRun('CEMS Home Screen — Module & Sub-link Reference')],
  }),
  new Paragraph({
    children: [new TextRun({ text: `Generated: ${new Date().toLocaleString()}`, italics: true, size: 20 })],
  }),
  new Paragraph({ children: [] }),
];

for (const section of sections) {
  children.push(
    new Paragraph({
      heading: HeadingLevel.HEADING_2,
      children: [new TextRun(`${section.num}. ${section.title}`)],
    }),
  );
  children.push(
    new Paragraph({
      children: [new TextRun({ text: section.description, italics: true, size: 22 })],
    }),
  );
  children.push(linkTable(section.items));
  if (section.note) {
    children.push(
      new Paragraph({
        children: [new TextRun({ text: section.note, italics: true, size: 20 })],
      }),
    );
  }
  if (section.extra) {
    for (const line of section.extra) {
      children.push(
        new Paragraph({
          children: [new TextRun({ text: line, size: 20 })],
        }),
      );
    }
  }
  children.push(new Paragraph({ children: [] }));
}

children.push(
  new Paragraph({ heading: HeadingLevel.HEADING_2, children: [new TextRun('Summary')] }),
  new Table({
    width: { size: 60, type: WidthType.PERCENTAGE },
    rows: [
      new TableRow({ children: [cell('Count', true), cell('Value', true)] }),
      new TableRow({ children: [cell('Category cards'), cell('11')] }),
      new TableRow({ children: [cell('Total sub-links'), cell('46')] }),
      new TableRow({
        children: [
          cell('Recent changes'),
          cell('Removed Memorandums & duplicate Commission; added Memos (/memos) & Trail Form List (/trail-form-list)'),
        ],
      }),
    ],
  }),
);

const doc = new Document({ sections: [{ children }] });

mkdirSync(exportDir, { recursive: true });
writeFileSync(outPath, await Packer.toBuffer(doc));

console.log(`Word document created:\n  ${outPath}`);
