import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests/visual',
  timeout: 30_000,
  retries: 0,
  use: {
    headless: true,
    viewport: { width: 1440, height: 1100 },
    ignoreHTTPSErrors: true,
  },
  projects: [
    {
      name: 'nextjs-baseline',
      use: {
        baseURL: 'http://localhost:3000',
      },
    },
    {
      name: 'wordpress-candidate',
      use: {
        baseURL: 'http://localhost:8080',
      },
    },
  ],
  webServer: [],
});
