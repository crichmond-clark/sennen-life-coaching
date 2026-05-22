# SEO Pass

Date: 2026-05-21
Branch: `feature/wordpress-seo-pass`

## Fixed

- Added shared Next.js SEO helpers in `lib/seo.ts` for canonical URLs, reusable page metadata, Open Graph/Twitter defaults, and JSON-LD builders.
- Updated Next.js page metadata for About, Services, Testimonials, and Booking to include canonical URLs and social metadata.
- Fixed Next.js social image references to the existing `/opengraph-image.jpg` route.
- Added Testimonials to the Next.js sitemap and kept Studio out of `robots.txt`.
- Added site-level `ProfessionalService` and `WebSite` JSON-LD to the Next.js shell.
- Added visible FAQ-based `FAQPage` JSON-LD to the Booking page.
- Improved WordPress theme SEO output: meta descriptions, Open Graph, Twitter cards, locale, default social image, and site-level JSON-LD.
- Added stable WordPress page descriptions for Home, About, Services, Testimonials, and Booking.
- Added WordPress FAQ JSON-LD to the booking/contact grid block.
- Added a WordPress theme fallback OG image at `wordpress/wp-content/themes/sennen/assets/images/og-image.jpg`.
- Added `noindex` for low-value WordPress search and 404 pages.

## Validation

- `npm run lint` passes with one pre-existing warning in `wordpress/wp-content/themes/sennen/assets/css/src/tailwind.config.js`.
- WordPress PHP lint passes in Docker for:
  - `wordpress/wp-content/themes/sennen/functions.php`
  - `wordpress/wp-content/plugins/sennen-core/src/blocks/booking-contact-grid/render.php`
- Rendered WordPress pages checked locally for title, meta description, canonical, OG URL, Twitter card, JSON-LD, and booking FAQ schema.
- `npm run build` currently fails with a Bus error in this environment before producing output; no TypeScript/ESLint errors were introduced by this pass.

## Remaining follow-ups

- Decide whether production will ship the Next.js app, WordPress site, or both; avoid duplicate indexable deployments for the same content.
- Add richer page-specific OG images if the brand needs better social sharing per route.
- Consider a dedicated local-business/contact schema pass if the business has a stable public address, phone number, opening hours, or service area wording.
- Fix existing type-check dependency gaps (`lucide-react`, Playwright, `pngjs`) so `npx tsc --noEmit` can be used as a validation gate.
