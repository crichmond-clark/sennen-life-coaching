# Astro Static Website Migration Plan

## Table of Contents

- [1. Problem Statement](#1-problem-statement)
- [2. Goals & Non-Goals](#2-goals--non-goals)
- [3. Proposed Architecture](#3-proposed-architecture)
- [4. Content and Copy Strategy](#4-content-and-copy-strategy)
- [5. Component Breakdown](#5-component-breakdown)
- [6. Data Flow](#6-data-flow)
- [7. Interface Contracts](#7-interface-contracts)
- [8. File Changes](#8-file-changes)
- [9. Implementation Phases](#9-implementation-phases)
- [10. Testing Strategy](#10-testing-strategy)
- [11. Security Implications](#11-security-implications)
- [12. Risks & Tradeoffs](#12-risks--tradeoffs)
- [13. Open Questions](#13-open-questions)

## 1. Problem Statement

Sennen Life Coaching is currently a Next.js 16 site with Sanity CMS, a Sanity Studio route, ISR-style content fetching, and a Resend-backed API route for the contact form.

For this project, that is probably more infrastructure than the site needs. The site is mostly static marketing content with a booking link/embed, contact form, services, testimonials, and SEO pages. Migrating to Astro would simplify hosting, reduce runtime dependencies, remove CMS/API complexity, and make the site easier to finish and maintain as a small static website.

The migration should preserve the existing page structure and visual direction while replacing dynamic/CMS behaviour with build-time content and static-friendly integrations.

## 2. Goals & Non-Goals

Goals:

- Migrate the public site from Next.js to Astro.
- Generate a fully static website with no required server runtime.
- Keep the current pages:
  - `/`
  - `/about`
  - `/services`
  - `/testimonials`
  - `/booking`
- Move public copy out of components and into content files.
- Use Astro content collections for typed, validated content.
- Use Markdown for prose-heavy page copy.
- Use YAML for structured human-edited content such as services, settings, testimonials, and FAQs.
- Remove Sanity Studio, Sanity schemas, GROQ queries, and ISR fetching.
- Remove the Next.js API route for contact and use a static-friendly form provider or `mailto:` fallback.
- Keep Calendly as an external booking embed or link.
- Keep SEO basics: title/description, Open Graph, Twitter image, sitemap, robots.txt, canonical URLs.
- Keep deployment simple: static hosting on Vercel, Netlify, Cloudflare Pages, or similar.
- Ensure the migration does not introduce WordPress or Payload CMS.

Non-Goals:

- No Payload CMS migration.
- No WordPress migration.
- No server-side rendering.
- No database.
- No Sanity Studio in the migrated site.
- No custom backend email service in the static MVP.
- No payment flow.
- No blog/journal unless added later.
- No full redesign unless required to make the static implementation work.

## 3. Proposed Architecture

Use Astro as a static site generator with local content files.

Recommended stack:

- **Astro** for static pages and components.
- **Astro content collections** for typed content loading and validation.
- **Markdown** for page-level prose.
- **YAML** for structured, human-edited content.
- **TypeScript** for typed data helpers.
- **Tailwind CSS v4** for styling, reusing the existing design tokens from `app/globals.css`.
- **Public static images** in `public/images/`.
- **Calendly embed/link** as client-side external script or simple external CTA.
- **Formspree, Netlify Forms, Basin, Getform, or mailto** for contact submissions.
- **@astrojs/sitemap** or a generated sitemap for SEO.

Key decision:

```txt
Static-first Astro site + Markdown/YAML content collections + external form provider
```

Why this over Payload/Sanity/WordPress:

- The site does not need a database or editorial workflow for launch.
- Static hosting is cheaper and simpler.
- There is less to configure: no CMS project, no CORS, no API tokens, no ISR, no server route for email.
- Content can still be updated by editing Markdown/YAML and redeploying.
- Markdown/YAML keeps the copy separate from layout code without adding CMS complexity.

Content editing model:

1. Target MVP: edit local Markdown/YAML files and redeploy.
2. Use the existing `content/*.json` files as migration input only, not the final canonical content model.
3. Optional later: add a Git-based/static CMS such as Decap CMS, CloudCannon, or Pages CMS if Sennen needs a browser-based editor.

## 4. Content and Copy Strategy

### Decision

Do **not** hardcode public page copy in Astro components.

Use this rule:

```txt
Components own layout and presentation.
Content files own public copy and editable business details.
```

### Preferred formats

Use a mixed content model:

| Content type | Format | Why |
|---|---|---|
| Long page prose | Markdown with YAML frontmatter | Best for headings, paragraphs, lists, and future rich text. |
| Services/prices/durations | YAML data entries | Easier for humans than JSON; comments and less punctuation. |
| Testimonials | YAML data entries | Simple structured records; easy to hide/remove. |
| Site settings | YAML data entry | Brand, contact, booking, and social links are structured. |
| FAQs | YAML data entries | Question/answer pairs are structured and reusable. |
| Nav labels and tiny UI strings | Hardcoded or small config | These are interface labels, not marketing copy. |
| Generated/API data | JSON only if needed | JSON is better for machine-generated data, but not necessary here. |

Why YAML over JSON for this site:

- Easier for non-developers to read and edit.
- Less noisy than JSON for marketing content.
- Supports comments.
- Works well with Astro data collections.
- Still validates with Zod through `src/content.config.ts`.

JSON is still acceptable for migration source data, but the final Astro version should prefer Markdown/YAML.

### Target content structure

```txt
src/
  content.config.ts
  content/
    pages/
      home.md
      about.md
      booking.md
    services/
      discovery-call.yaml
      one-to-one-coaching.yaml
      coaching-package.yaml
    testimonials/
      testimonial-1.yaml
    settings/
      site.yaml
    faqs/
      booking.yaml
```

Notes:

- `pages/*.md` is for prose-heavy pages.
- `services/*.yaml` is for repeatable service cards.
- `settings/site.yaml` is a singleton-style settings entry.
- `testimonials/*.yaml` should only exist for real testimonials.
- `faqs/*.yaml` is optional, but useful for the booking page.

### Example: page content

`src/content/pages/home.md`

```md
---
title: Sennen Life Coaching
seoTitle: Sennen Life Coaching | Rooted in Grace
seoDescription: A calm, supportive coaching space for people navigating change.
hero:
  heading: Rooted in Grace
  subtitle: A calm, supportive coaching space for people navigating change.
  primaryCtaText: Book a session
  primaryCtaHref: /booking
  secondaryCtaText: Learn about Sennen
  secondaryCtaHref: /about
sections:
  philosophyHeading: Coaching that meets you where you are
---

This is where longer homepage prose can live as Markdown.

Use Markdown when the copy needs paragraphs, emphasis, or lists.
```

### Example: service content

`src/content/services/one-to-one-coaching.yaml`

```yaml
title: One-to-one coaching
slug: one-to-one-coaching
summary: A supportive coaching session focused on clarity, confidence, and next steps.
duration: 60 minutes
price: Enquire for pricing
format: Online
sortOrder: 10
features:
  - Space to talk through what feels stuck
  - Practical reflection and next steps
  - Gentle accountability between sessions
ctaText: Book a session
ctaHref: /booking
```

### Example: site settings

`src/content/settings/site.yaml`

```yaml
brandName: Sennen Life Coaching
tagline: Rooted in Grace
contactEmail: hello@example.com
bookingUrl: https://calendly.com/example
socialLinks:
  - platform: Instagram
    url: ""
  - platform: Facebook
    url: ""
```

### What can remain hardcoded

Hardcode small stable UI labels where content editing is unlikely to matter:

- `Home`
- `About`
- `Services`
- `Testimonials`
- `Booking`
- `Name`
- `Email`
- `Message`
- `Send Message`

Do not hardcode:

- Hero headings.
- Service descriptions.
- Prices/durations.
- Testimonials.
- About/story copy.
- Booking instructions.
- SEO titles/descriptions.
- Social links.
- Contact email.

### Validation

Use Astro content collections with Zod schemas in `src/content.config.ts`.

Validation should catch:

- Missing title/description fields.
- Invalid URL fields.
- Empty service title/slug.
- Invalid sort order.
- Social links with unsupported platform names.
- Testimonials missing quote or author.

## 5. Component Breakdown

- **Astro Layout**
  - Owns HTML shell, fonts, metadata, canonical links, global styles, navbar, and footer.
  - Replaces `app/layout.tsx`.

- **Page Components**
  - `src/pages/index.astro`
  - `src/pages/about.astro`
  - `src/pages/services.astro`
  - `src/pages/testimonials.astro`
  - `src/pages/booking.astro`
  - Replace the current Next.js route files.

- **Shared Astro Components**
  - `NavBar.astro`
  - `Footer.astro`
  - `Testimonials.astro`
  - `BookingEmbed.astro`
  - `ContactForm.astro`
  - Use plain Astro + small inline scripts where interactivity is needed.

- **Content Collections**
  - `src/content.config.ts` defines `pages`, `services`, `testimonials`, `settings`, and optional `faqs` collections.
  - Replaces Sanity `fetch.ts`, `queries.ts`, `client.ts`, `image.ts`, and `types.ts`.

- **Static Contact Form**
  - Posts to external form provider.
  - Replaces `app/api/contact/route.ts` and removes Resend dependency from this repo.

- **Static SEO**
  - Page metadata configured in content frontmatter and passed into the base layout.
  - Sitemap generated at build or provided as static XML.
  - Robots provided as `public/robots.txt`.

## 6. Data Flow

Current flow:

```txt
Sanity Studio / content JSON
→ Sanity dataset
→ Next.js fetches via GROQ
→ ISR page render
→ Contact form POSTs to Next API route
→ Resend sends email
```

Target Astro static flow:

```txt
Markdown/YAML content collections
→ Astro validates content at build time
→ Astro renders static HTML/CSS/JS into dist/
→ Static host serves files
→ Contact form posts to external provider or opens mailto
→ Calendly loads from external Calendly script/link
```

Content update flow:

```txt
Edit src/content/**/*.md or src/content/**/*.yaml
→ npm run check
→ npm run build
→ Deploy static dist/
→ Site updates
```

Optional Git CMS flow later:

```txt
Sennen edits content in Git-backed CMS
→ CMS commits Markdown/YAML file changes
→ Hosting provider rebuilds Astro site
→ Static site updates
```

## 7. Interface Contracts

### Content entry: `src/content/settings/site.yaml`

Fields:

- `brandName: string`
- `tagline: string`
- `bookingUrl: string`
- `contactEmail: string`
- `socialLinks: { platform: string; url: string }[]`

Used by:

- Header brand.
- Footer brand/socials.
- Booking CTA target.
- Contact fallback email.

Error cases:

- Empty booking URL: show contact CTA or “booking coming soon” fallback.
- Empty social URL: hide that social link.
- Invalid URL: fail content validation unless intentionally empty.

### Content entries: `src/content/pages/*.md`

Recommended page entries:

- `home.md`
- `about.md`
- `booking.md`

Common frontmatter fields:

- `title`
- `seoTitle`
- `seoDescription`
- `hero.heading`
- `hero.subtitle`
- `hero.primaryCtaText`
- `hero.primaryCtaHref`
- optional `hero.secondaryCtaText`
- optional `hero.secondaryCtaHref`

Used by:

- Page rendering.
- SEO metadata.
- Hero sections.
- Page-specific content sections.

Error cases:

- Missing SEO title/description: fail validation or use a safe project-wide fallback.
- Missing hero fields: fail validation for public pages.

### Content entries: `src/content/services/*.yaml`

Fields:

- `title`
- `slug`
- `summary`
- `duration`
- `price`
- `format`
- `sortOrder`
- `features[]`
- `ctaText`
- `ctaHref`

Used by:

- Services page cards.
- Homepage service preview.

Error cases:

- Duplicate slug: fail in helper logic or review manually.
- Missing price/duration: allow “Enquire” only if intentional.

### Content entries: `src/content/testimonials/*.yaml`

Fields:

- `quote`
- `authorName`
- optional `authorTitle`
- optional `relationship`
- optional `sortOrder`

Used by:

- Testimonials page.
- Homepage testimonial section if real testimonials exist.

Important rule:

- Use real testimonials only. If no real testimonials exist, leave this collection empty and replace the public section with “What to expect”.

### Optional content entries: `src/content/faqs/*.yaml`

Fields:

- `question`
- `answer`
- `page`
- `sortOrder`

Used by:

- Booking page FAQ.
- Optional services page FAQ.

### Contact form provider

Option A — Formspree/Basin/Getform:

- Form action: provider endpoint URL.
- Method: `POST`.
- Fields: name, email, inquiryType, message.
- Spam protection: provider honeypot/recaptcha option if available.

Option B — Netlify Forms:

- Requires Netlify hosting or compatible Netlify form handling.
- Form includes `data-netlify="true"` and hidden form name.

Option C — mailto fallback:

- No form submission tracking.
- Opens user email client.
- Lowest dependency option.

## 8. File Changes

Create:

- `astro.config.mjs` — Astro config, static output, integrations.
- `src/content.config.ts` — Astro content collection schemas.
- `src/content/pages/home.md` — homepage copy and metadata.
- `src/content/pages/about.md` — about page copy and metadata.
- `src/content/pages/booking.md` — booking page copy and metadata.
- `src/content/services/*.yaml` — service cards/packages.
- `src/content/testimonials/*.yaml` — real testimonial entries, if available.
- `src/content/settings/site.yaml` — brand/contact/booking/social settings.
- Optional: `src/content/faqs/*.yaml` — booking/services FAQ entries.
- `src/layouts/BaseLayout.astro` — shared document shell and SEO props.
- `src/pages/index.astro` — homepage.
- `src/pages/about.astro` — about page.
- `src/pages/services.astro` — services page.
- `src/pages/testimonials.astro` — testimonials page.
- `src/pages/booking.astro` — booking/contact page.
- `src/components/NavBar.astro` — static nav with small mobile-menu script.
- `src/components/Footer.astro` — footer and social links.
- `src/components/Testimonials.astro` — testimonial cards.
- `src/components/BookingEmbed.astro` — Calendly embed/link.
- `src/components/ContactForm.astro` — static provider form.
- `src/lib/content.ts` — content helpers, sorting, singleton access.
- `src/styles/global.css` — migrated Tailwind/theme styles from `app/globals.css`.
- `public/robots.txt` — static robots file.
- Optional: `src/pages/sitemap.xml.ts` if not using `@astrojs/sitemap`.

Modify:

- `package.json` — replace Next/Sanity scripts and dependencies with Astro scripts/dependencies.
- `tsconfig.json` — Astro-compatible TypeScript config.
- `postcss.config.mjs` or Vite/Tailwind config — make Tailwind work with Astro.
- `README.md` — update setup, content editing, build, and deployment docs.
- `.env.example` — remove Sanity/Resend requirements; add static form endpoint if used.

Use as migration input, then remove or archive:

- `content/site-settings.json`
- `content/home.json`
- `content/about.json`
- `content/booking.json`
- `content/services.json`
- `content/testimonials.json`

Keep:

- `public/images/*`.
- `public/favicon.svg`.

Delete after migration is complete:

- `app/` — Next.js app routes and API route.
- `components/` — old React components after they are ported.
- `sanity/` — Sanity client, schemas, types, queries.
- `sanity.config.ts`.
- `sanity.cli.ts`.
- `scripts/push-content.ts`.
- `scripts/seed-sanity.ts`.
- `scripts/seed-sanity-manual.ts`.
- `next.config.ts`.
- `vercel.json` if it only contains Next-specific config.
- Sanity/Next/React-specific dependencies no longer used.

## 9. Implementation Phases

### Phase 1 — Astro and content foundation

Branch: `feature/astro-static-migration`

Commits:

- [ ] Add Astro config and Astro scripts.
- [ ] Add Tailwind/global CSS setup.
- [ ] Add base layout with fonts, metadata props, navbar/footer slots.
- [ ] Add `src/content.config.ts` with Zod schemas for pages, settings, services, testimonials, and optional FAQs.
- [ ] Convert existing JSON content into target Markdown/YAML content files.
- [ ] Add content helper functions for singleton settings, sorted services, sorted testimonials, and page lookup.

Done when:

- `npm run dev` starts an Astro site.
- A placeholder homepage renders with existing global styles.
- Content collections validate successfully.

Validation:

```bash
cd /home/kon/repos/sennen-life-coaching
npm run dev
npm run check
npm run build
```

### Phase 2 — Port shared components

Commits:

- [ ] Port `NavBar.tsx` to `NavBar.astro`.
- [ ] Replace React pathname active-state with Astro URL/path logic.
- [ ] Replace scroll/mobile menu React state with a small inline client script.
- [ ] Port `Footer.tsx` to `Footer.astro`.
- [ ] Port social icons as inline SVG or a lightweight Astro icon package.
- [ ] Port testimonial card component.

Done when:

- Header, mobile menu, footer, and testimonials render without React runtime.

Validation:

```bash
npm run build
```

Manual checks:

- Desktop nav.
- Mobile nav open/close.
- Social links hidden when URLs are empty.

### Phase 3 — Port pages

Commits:

- [ ] Port homepage to `src/pages/index.astro`.
- [ ] Port about page.
- [ ] Port services page.
- [ ] Port testimonials page.
- [ ] Port booking page.
- [ ] Use content collections everywhere instead of Sanity fetch calls or hardcoded public copy.

Done when:

- All current public routes exist and render from Markdown/YAML content.

Validation:

```bash
npm run check
npm run build
npm run preview
```

Manual route checks:

- `/`
- `/about`
- `/services`
- `/testimonials`
- `/booking`

### Phase 4 — Replace dynamic integrations with static-friendly integrations

Commits:

- [ ] Replace `/api/contact` POST with chosen static form provider.
- [ ] Add honeypot/spam protection if the provider supports it.
- [ ] Keep Calendly embed or replace with a simple external booking CTA.
- [ ] Remove Resend from project dependencies if no server route remains.
- [ ] Update `.env.example` with only static-safe public variables if needed.

Done when:

- Contact form can submit successfully without a site backend.
- Booking still works.

Validation:

```bash
npm run build
npm run preview
```

Manual checks:

- Submit valid contact form.
- Test invalid/missing form fields if client-side validation is retained.
- Test booking link/embed on mobile.

### Phase 5 — Copy cleanup and content launch pass

Commits:

- [ ] Remove placeholder copy from Markdown/YAML content files.
- [ ] Replace or remove Bali/Ubud/Thailand/retreat language unless it is truly part of Sennen's offer.
- [ ] Confirm services, prices, durations, format, and booking copy.
- [ ] Hide testimonials if they are not real.
- [ ] Confirm contact email, booking URL, and social links.

Done when:

- The public site content is real, launch-safe, and editable through content files.

Validation:

```bash
npm run check
npm run build
npm run preview
```

### Phase 6 — SEO and deployment polish

Commits:

- [ ] Add page-specific metadata through page frontmatter and `BaseLayout.astro`.
- [ ] Add Open Graph and Twitter metadata.
- [ ] Add canonical URLs.
- [ ] Generate sitemap.
- [ ] Add static robots.txt.
- [ ] Move/copy OG images to stable public paths.
- [ ] Update README for Astro static workflow and content editing.

Done when:

- Static build has correct SEO artefacts.
- README no longer references Sanity/Next/Resend as the active stack.

Validation:

```bash
npm run check
npm run build
npm run preview
```

Manual checks:

- View page source for metadata.
- Check `/robots.txt`.
- Check `/sitemap-index.xml` or `/sitemap.xml` depending on integration.

### Phase 7 — Remove legacy Next/Sanity code

Commits:

- [ ] Remove Next app files after Astro route parity is confirmed.
- [ ] Remove Sanity config/schemas/scripts.
- [ ] Remove unused dependencies.
- [ ] Regenerate lockfile.
- [ ] Run final build.

Done when:

- Repo is a clean Astro static site with no active Next/Sanity runtime code.

Validation:

```bash
npm install
npm run check
npm run build
npm run preview
```

### Phase 8 — Static launch review

Commits:

- [ ] Deploy static site.
- [ ] Confirm custom domain.
- [ ] Test contact form in production.
- [ ] Test booking in production.
- [ ] Capture screenshots for Askeladd case study.

Done when:

- The live site works end-to-end as a static website.

## 10. Testing Strategy

Automated checks:

- `npm run check` for Astro/content validation.
- `npm run build` for every phase.
- Content collection schemas with Zod for required fields and valid URLs.

Manual checks:

- Page route parity: every existing public route exists in Astro.
- Mobile viewport checks: 375px, 768px, desktop.
- Keyboard navigation for nav and form.
- Contact form submission.
- Booking embed/link.
- Empty social links are hidden.
- Testimonials hidden if none are real.
- SEO source check for title, description, OG image, canonical.

Optional tests:

- Playwright smoke test for static pages if desired.
- Link checker after deploy.
- Lighthouse performance/accessibility/SEO pass.

## 11. Security Implications

Security improves because the migrated site has no first-party server runtime, no Sanity API token, no Sanity Studio route, and no Resend API key in the deployed app.

Remaining considerations:

- Contact form data is processed by the chosen external provider; choose one with spam controls and acceptable privacy terms.
- Do not expose private email addresses unless intended.
- Calendly embed loads third-party JavaScript.
- External links should use `rel="noopener noreferrer"` when opening in new tabs.
- If using a Git-based CMS later, protect GitHub/Netlify/CloudCannon auth properly.
- If retaining client-side form validation, remember it is UX only; the external provider must still validate/spam-filter server-side.

No sensitive secrets should be required in the static site build unless a form provider uses a public endpoint ID.

## 12. Risks & Tradeoffs

- Risk: Sennen loses Sanity Studio editing.
  - Mitigation: keep content as simple Markdown/YAML, document editing, and optionally add Decap CMS, CloudCannon, or Pages CMS later.

- Risk: YAML can be indentation-sensitive.
  - Mitigation: keep schemas simple, add examples, and rely on `npm run check` to catch invalid files.

- Risk: Contact form no longer uses Resend.
  - Mitigation: choose a static form provider, or accept a `mailto:` fallback for the simplest launch.

- Risk: Porting React components to Astro takes longer than expected.
  - Mitigation: use `@astrojs/react` temporarily for hard-to-port components, then remove React once stable. Prefer pure Astro for final static simplicity.

- Risk: Astro/Tailwind v4 setup differs from current Next setup.
  - Mitigation: migrate global CSS first and validate early before porting every page.

- Risk: Image optimization changes.
  - Mitigation: keep images in `public/images` initially; optimise later with Astro assets if needed.

- Risk: Existing placeholder content survives the technical migration.
  - Mitigation: run Phase 5 as a dedicated content launch pass before deployment.

- Risk: Static site cannot support future dynamic features.
  - Mitigation: add external tools for booking/forms; only reintroduce server routes if a real feature requires them.

## 13. Open Questions

These must be resolved before implementation begins:

1. **Contact form provider:** Formspree, Netlify Forms, Basin/Getform, or `mailto:`?
2. **Hosting:** Vercel static, Netlify, Cloudflare Pages, or another host?
3. **Browser-based editing:** is editing Markdown/YAML in the repo acceptable, or does Sennen need a Git-backed CMS?
4. **Calendly:** keep inline embed, or use a simpler external “Book a session” link?
5. **Testimonials:** are current testimonials real? If not, remove/hide before launch.
6. **Images:** keep current stock images for launch, or replace during migration?
7. **Copy cleanup timing:** migrate first then rewrite copy, or rewrite copy while porting pages?

Recommended answers for the fastest launch:

- Use **Astro static output**.
- Use **Markdown for page copy**.
- Use **YAML for settings, services, testimonials, and FAQs**.
- Validate everything with **Astro content collections + Zod**.
- Use **Formspree** or **Netlify Forms** for contact.
- Keep **Calendly as a link or embed**.
- Hide testimonials unless real.
- Migrate the content model during the Astro migration, then do a focused copy/content launch pass.
