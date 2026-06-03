# Sennen Life Coaching — Post-Migration TODO

This tracks what is still needed after the static Astro migration before calling the site launch-ready.

## Table of Contents

- [Branch and Review](#branch-and-review)
- [Content Cleanup](#content-cleanup)
- [Booking and Contact](#booking-and-contact)
- [Assets and Branding](#assets-and-branding)
- [SEO and Launch Checks](#seo-and-launch-checks)
- [Deployment](#deployment)

## Branch and Review

- [ ] Review all uncommitted migration changes on `feature/astro-static-migration`.
- [ ] Confirm deleted Next.js, Sanity, and Resend files are no longer needed.
- [ ] Run one final local review in browser across mobile and desktop sizes.
- [ ] Commit only after manual review.

## Content Cleanup

- [ ] Replace placeholder/AI-sounding copy with real Sennen-approved copy.
- [ ] Confirm service names, descriptions, durations, prices, and CTAs in `src/content/services/*.yaml`.
- [ ] Confirm testimonials are real and approved before publishing them.
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
  - [ ] `/testimonials`
  - [ ] `/booking`
- [ ] Confirm `robots.txt` and generated sitemap URLs point to `https://sennenlifecoaching.com`.
- [ ] Check metadata/OG preview with a sharing debugger after deployment.

## Deployment

- [ ] Update host settings if needed:
  - build command: `npm run build`
  - output directory: `dist`
- [ ] Deploy preview from `feature/astro-static-migration`.
- [ ] Review preview URL with Sennen.
- [ ] Merge/deploy only after content, booking, and contact are approved.
