import { execSync } from 'child_process';
import { cpSync, existsSync, rmSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const dest = join(root, '..', 'public', 'documentation');

process.chdir(root);
process.env.DOCUSAURUS_BASE_URL = '/documentation/';
process.env.DOCUSAURUS_URL = process.env.DOCUSAURUS_URL || 'http://127.0.0.1:8000';

console.log('Building CEMS docs for Laravel (baseUrl: /documentation/)...');
execSync('npx docusaurus build', { stdio: 'inherit' });

const source = join(root, 'build');
if (!existsSync(source)) {
  console.error('Build folder not found after docusaurus build.');
  process.exit(1);
}

if (existsSync(dest)) {
  rmSync(dest, { recursive: true, force: true });
}

cpSync(source, dest, { recursive: true });
console.log(`\nDone. Open http://127.0.0.1:8000/documentation/docs/system-features`);
