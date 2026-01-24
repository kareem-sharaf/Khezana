/**
 * Script to copy manifest.json from .vite folder to build folder
 * This is needed because Vite 7 places manifest.json in .vite subfolder
 * but Laravel expects it in the build folder root
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const src = path.join(__dirname, '../public/build/.vite/manifest.json');
const dest = path.join(__dirname, '../public/build/manifest.json');

if (fs.existsSync(src)) {
    fs.copyFileSync(src, dest);
    console.log('✅ Manifest copied successfully from .vite/manifest.json to manifest.json');
} else {
    console.log('⚠️  Manifest not found in .vite folder - this is normal if using dev server');
}
