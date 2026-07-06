import { execSync } from 'child_process';
import { createWriteStream, existsSync, mkdirSync, readdirSync, readFileSync, statSync, writeFileSync } from 'fs';
import { dirname, join, relative } from 'path';
import { fileURLToPath } from 'url';
import { mdToPdf } from 'md-to-pdf';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const docsDir = join(root, 'docs');
const exportDir = join(root, 'export');
const pdfPath = join(exportDir, 'CEMS-Documentation.pdf');

/** Read order mirrors the sidebar: intro → getting-started → core → overview → modules → api */
const ORDER = [
  'intro.md',
  'getting-started/login.md',
  'getting-started/installation.md',
  'getting-started/permissions.md',
  'core/home.md',
  'core/dashboard.md',
  'core/team-chat.md',
  'overview/system-features.md',
  'modules/car-reports/index.md',
  'modules/car-reports/unit-report.md',
  'modules/car-reports/pricelist.md',
  'modules/car-reports/unit-report-archiving.md',
  'modules/staff-reports/index.md',
  'modules/payments-expenses/index.md',
  'modules/vlogs-posts/index.md',
  'modules/transfers-papers/index.md',
  'modules/customer-lists/index.md',
  'modules/equipment-lists/index.md',
  'modules/company-documents/index.md',
  'modules/analytics-report/index.md',
  'modules/settings/index.md',
  'modules/compare/index.md',
  'api/overview.md',
];

function collectMdFiles(dir, files = []) {
  for (const entry of readdirSync(dir)) {
    const full = join(dir, entry);
    if (statSync(full).isDirectory()) {
      collectMdFiles(full, files);
    } else if (entry.endsWith('.md') && !entry.startsWith('_')) {
      files.push(relative(docsDir, full).replace(/\\/g, '/'));
    }
  }
  return files;
}

function stripFrontmatter(content) {
  return content.replace(/^---[\s\S]*?---\n*/, '');
}

function buildCombinedMarkdown() {
  const allFiles = collectMdFiles(docsDir);
  const ordered = [
    ...ORDER.filter((f) => allFiles.includes(f)),
    ...allFiles.filter((f) => !ORDER.includes(f)).sort(),
  ];

  const parts = [
    '# Car Empire Management System — Full Documentation\n',
    `*Generated ${new Date().toLocaleString()}*\n`,
    '---\n',
  ];

  for (const file of ordered) {
    const full = join(docsDir, file);
    const body = stripFrontmatter(readFileSync(full, 'utf8'));
    parts.push(`\n\n<div style="page-break-before: always"></div>\n\n`);
    parts.push(body.trim());
    parts.push('\n\n---\n');
  }

  return parts.join('');
}

async function main() {
  mkdirSync(exportDir, { recursive: true });

  const combinedPath = join(exportDir, 'CEMS-Documentation-combined.md');
  console.log('Combining markdown files...');
  writeFileSync(combinedPath, buildCombinedMarkdown(), 'utf8');

  console.log('Generating PDF (this may take a minute)...');
  await mdToPdf(
    { content: readFileSync(combinedPath, 'utf8') },
    {
      dest: pdfPath,
      pdf_options: {
        format: 'A4',
        margin: { top: '20mm', right: '15mm', bottom: '20mm', left: '15mm' },
        printBackground: true,
      },
      css: `
        body { font-family: Segoe UI, Arial, sans-serif; font-size: 11pt; line-height: 1.5; }
        h1 { font-size: 22pt; border-bottom: 2px solid #2563eb; padding-bottom: 6px; }
        h2 { font-size: 16pt; color: #1e40af; margin-top: 1.5em; }
        h3 { font-size: 13pt; }
        table { border-collapse: collapse; width: 100%; margin: 1em 0; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; }
        code { background: #f1f5f9; padding: 2px 4px; border-radius: 3px; font-size: 10pt; }
        pre { background: #1e293b; color: #e2e8f0; padding: 12px; border-radius: 6px; overflow-x: auto; }
        pre code { background: none; color: inherit; }
        blockquote { border-left: 4px solid #2563eb; margin: 1em 0; padding: 0.5em 1em; background: #f8fafc; }
      `,
    },
  );

  console.log('\nPDF created:');
  console.log(`  ${pdfPath}`);
  console.log('\nNote: Mermaid flowcharts appear as code blocks in PDF.');
  console.log('For diagrams, use the offline ZIP + browser, or Print to PDF from the live site.');
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
