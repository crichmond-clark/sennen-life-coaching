import * as fs from 'fs';
import * as path from 'path';
import PNG from 'pngjs';
import pixelmatch from 'pixelmatch';

const screenshotsDir = path.resolve(__dirname, '__screenshots__');
const nextDir = path.join(screenshotsDir, 'nextjs-baseline');
const wpDir = path.join(screenshotsDir, 'wordpress-candidate');
const diffDir = path.join(screenshotsDir, 'diff');

function compareScreenshots() {
  fs.mkdirSync(diffDir, { recursive: true });

  const nextFiles = fs.readdirSync(nextDir).filter((f) => f.endsWith('.png'));
  const wpFiles = fs.readdirSync(wpDir).filter((f) => f.endsWith('.png'));

  const allFiles = new Set([...nextFiles, ...wpFiles]);
  let totalPassed = 0;
  let totalFailed = 0;
  const results: { file: string; status: string; diffPercent: number }[] = [];

  for (const file of sorted(allFiles)) {
    const nextPath = path.join(nextDir, file);
    const wpPath = path.join(wpDir, file);

    if (!fs.existsSync(nextPath) || !fs.existsSync(wpPath)) {
      console.log(`⚠️  ${file}: MISSING (${!fs.existsSync(nextPath) ? 'no next' : 'no wp'})`);
      totalFailed++;
      results.push({ file, status: 'missing', diffPercent: -1 });
      continue;
    }

    const imgNext = PNG.sync.read(fs.readFileSync(nextPath));
    const imgWp = PNG.sync.read(fs.readFileSync(wpPath));

    if (imgNext.width !== imgWp.width || imgNext.height !== imgWp.height) {
      console.log(
        `⚠️  ${file}: SIZE MISMATCH next=${imgNext.width}x${imgNext.height} wp=${imgWp.width}x${imgWp.height}`,
      );
      totalFailed++;
      results.push({ file, status: 'size-mismatch', diffPercent: -1 });
      continue;
    }

    const { width, height } = imgNext;
    const diff = new PNG({ width, height });
    const numDiffPixels = pixelmatch(imgNext.data, imgWp.data, diff.data, width, height, {
      threshold: 0.1,
    });
    const totalPixels = width * height;
    const diffPercent = (numDiffPixels / totalPixels) * 100;

    const status = diffPercent < 0.5 ? 'PASS' : 'FAIL';
    if (status === 'PASS') {
      totalPassed++;
    } else {
      totalFailed++;
      fs.writeFileSync(path.join(diffDir, file), PNG.sync.write(diff));
    }

    console.log(`${status === 'PASS' ? '✅' : '❌'} ${file}: ${diffPercent.toFixed(3)}% diff`);
    results.push({ file, status, diffPercent });
  }

  console.log(`\nResults: ${totalPassed} passed, ${totalFailed} failed`);

  // Write JSON report
  fs.writeFileSync(
    path.join(diffDir, 'report.json'),
    JSON.stringify({ results, passed: totalPassed, failed: totalFailed }, null, 2),
  );

  process.exit(totalFailed > 0 ? 1 : 0);
}

function sorted(s: Set<string>): string[] {
  return Array.from(s).sort();
}

compareScreenshots();
