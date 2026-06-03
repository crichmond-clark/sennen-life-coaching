# Sennen Life Coaching — Post-Migration TODO

This tracks what is still needed after the static Astro migration before calling the site launch-ready.

## Table of Contents

- [Current Status](#current-status)
- [Production-Ready Priorities](#production-ready-priorities)
- [Branch and Review](#branch-and-review)
- [Content Cleanup](#content-cleanup)
- [Booking and Contact](#booking-and-contact)
- [Assets and Branding](#assets-and-branding)
- [SEO and Launch Checks](#seo-and-launch-checks)
- [Deployment](#deployment)

## Current Status

- Astro migration is on `feature/astro-static-migration`.
- Testimonials are intentionally hidden from the nav and home page until real, approved testimonials are ready.
- The testimonials route file is parked as `src/pages/_testimonials.astro` so it can be restored later without publishing `/testimonials` now.
- The home hero scroll indicator uses the spacing pattern from the WordPress branch: it sits in the hero content flow instead of being absolutely pinned to the bottom.

## Production-Ready Priorities

1. Add the real booking URL.
2. Decide contact handling and test it end-to-end.
3. Replace or approve every public image.
4. Replace placeholder/AI-sounding copy with Sennen-approved copy.
5. Keep testimonials disabled until quotes are real and approved.
6. Run final mobile/desktop QA on a deploy preview.
7. Confirm metadata, sitemap, robots, favicon, and OG image before production deploy.

## Branch and Review

- [ ] Review all uncommitted migration changes on `feature/astro-static-migration`.
- [ ] Confirm deleted Next.js, Sanity, and Resend files are no longer needed.
- [ ] Run one final local review in browser across mobile and desktop sizes.
- [ ] Commit only after manual review.

## Content Cleanup

- [ ] Replace placeholder/AI-sounding copy with real Sennen-approved copy.
- [ ] Confirm service names, descriptions, durations, prices, and CTAs in `src/content/services/*.yaml`.
- [ ] Keep testimonials hidden until real quotes are approved; then restore `src/pages/_testimonials.astro` to `src/pages/testimonials.astro` and re-add the nav link.
- [ ] Update page SEO titles/descriptions in `src/content/pages/*.md`.
- [ ] Check all page copy for claims that need softening or substantiation.

## Booking and Contact

- [ ] Add the real Calendly URL to `bookingUrl` in `src/content/settings/site.yaml`.
- [ ] Choose contact handling:
  - [ ] Keep `mailto:` fallback temporarily, or
  - [ ] Add a Formspree/Basin/Getform endpoint to `formAction`.
- [ ] Send a test enquiry through the chosen contact flow.
- [ ] Confirm the public contact email is correct.

## Assets and Branding

- [ ] Replace stock/placeholder images with approved images where available.
- [ ] Confirm favicon, Apple touch icon, and Open Graph image are correct.
- [ ] Check footer/social links and remove anything Sennen does not use.
- [ ] Confirm the brand name is consistently `Sennen Life Coaching`.

## SEO and Launch Checks

- [ ] Run `npm run check`.
- [ ] Run `npm run build`.
- [ ] Run `npm audit --omit=dev`.
- [ ] Preview with `npm run preview` and manually check all routes:
  - [ ] `/`
  - [ ] `/about`
  - [ ] `/services`
  - [ ] `/booking`
- [ ] Confirm `/testimonials` is not linked or generated until testimonials are approved.
- [ ] Confirm `robots.txt` and generated sitemap URLs point to `https://sennenlifecoaching.com`.
- [ ] Check metadata/OG preview with a sharing debugger after deployment.

## Deployment

- [ ] Update host settings if needed:
  - build command: `npm run build`
  - output directory: `dist`
- [ ] Deploy preview from `feature/astro-static-migration`.
- [ ] Review preview URL with Sennen.
- [ ] Merge/deploy only after content, booking, and contact are approved.
