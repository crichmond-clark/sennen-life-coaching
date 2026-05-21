import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';

const ROUTES = ['/', '/about', '/services', '/testimonials', '/booking'];

const VIEWPORTS: Record<string, { width: number; height: number }> = {
  mobile: { width: 375, height: 900 },
  tablet: { width: 768, height: 1024 },
  desktop: { width: 1440, height: 1100 },
  'large-desktop': { width: 1920, height: 1200 },
};

const screenshotsDir = path.resolve(__dirname, '__screenshots__');

test.describe('Visual baseline capture', () => {
  for (const route of ROUTES) {
    for (const [vpName, viewport] of Object.entries(VIEWPORTS)) {
      test(`${route} @ ${vpName}`, async ({ page }) => {
        await page.setViewportSize(viewport);
        await page.goto(route, { waitUntil: 'networkidle' });
        // Wait for fonts and images to settle
        await page.waitForTimeout(1000);
        const safeName = route === '/' ? 'home' : route.replace(/^\//, '');
        const filename = `${safeName}--${vpName}.png`;
        const dir = path.join(screenshotsDir, test.info().project.name);
        fs.mkdirSync(dir, { recursive: true });
        await page.screenshot({
          path: path.join(dir, filename),
          fullPage: true,
        });
        // Sanity: file exists and is non-trivial
        const stat = fs.statSync(path.join(dir, filename));
        expect(stat.size).toBeGreaterThan(1000);
      });
    }
  }
});
