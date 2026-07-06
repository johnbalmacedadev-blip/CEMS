import { execSync } from 'child_process';
import { createWriteStream, existsSync, mkdirSync, writeFileSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';
import archiver from 'archiver';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const exportDir = join(root, 'export');
const zipName = 'CEMS-Documentation-Offline.zip';
const zipPath = join(exportDir, zipName);

function buildOfflineSite() {
  process.chdir(root);
  process.env.DOCUSAURUS_BASE_URL = './';
  process.env.DOCUSAURUS_URL = 'http://localhost';
  console.log('Building offline HTML site (relative paths)...');
  execSync('npx docusaurus build', { stdio: 'inherit' });
}

function zipBuild() {
  const source = join(root, 'build');
  if (!existsSync(source)) {
    throw new Error('Build folder missing. Build step failed.');
  }

  mkdirSync(exportDir, { recursive: true });

  return new Promise((resolve, reject) => {
    const output = createWriteStream(zipPath);
    const archive = archiver('zip', { zlib: { level: 9 } });

    output.on('close', () => resolve());
    archive.on('error', reject);

    archive.pipe(output);
    archive.directory(source, false);
    archive.finalize();
  });
}

function writeReadme() {
  writeFileSync(
    join(exportDir, 'README-OFFLINE.txt'),
    `# CEMS Documentation — Offline Package

Extract the ZIP anywhere on your computer.

## Recommended — local web server

Flowcharts and navigation work best with a tiny server:

  cd path/to/extracted/folder
  python -m http.server 8080

Then open: http://localhost:8080/docs/intro/

## With XAMPP / Laravel

Copy extracted files into public/documentation, then visit:
  http://127.0.0.1:8000/documentation/docs/intro

## Regenerate

  cd documentation
  npm run export:offline
`,
  );
}

async function main() {
  buildOfflineSite();
  await zipBuild();
  writeReadme();

  console.log('\nOffline package created:');
  console.log(`  ${zipPath}`);

  console.log('\nRestoring Laravel web build (public/documentation)...');
  execSync('node scripts/build-laravel.mjs', { stdio: 'inherit', cwd: root });
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
