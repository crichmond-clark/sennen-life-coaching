# WordPress Migration Plan

## Table of Contents

- [1. Problem Statement](#1-problem-statement)
- [2. Goals & Non-Goals](#2-goals-non-goals)
- [3. Proposed Architecture](#3-proposed-architecture)
- [4. Component Breakdown](#4-component-breakdown)
- [5. Data Flow](#5-data-flow)
- [6. Interface Contracts](#6-interface-contracts)
- [7. File Changes](#7-file-changes)
- [8. Implementation Phases](#8-implementation-phases)
- [9. Testing Strategy](#9-testing-strategy)
- [10. Security Implications](#10-security-implications)
- [11. Risks & Tradeoffs](#11-risks-tradeoffs)
- [12. Open Questions](#12-open-questions)

## 1. Problem Statement

The current Sennen Life Coaching site is a Next.js 16 application using Sanity CMS for content, Vercel-oriented deployment, Resend for contact emails, and Calendly for booking. It currently supports:

- Home page content via Sanity `home`
- About page content via Sanity `about`
- Services via Sanity `service` documents
- Testimonials via Sanity `testimonial` documents
- Booking page content via Sanity `booking`
- Site-wide settings via Sanity `siteSettings`
- Contact form via Next.js API route + Resend
- Static assets in `public/images/`

The desired target is a self-hosted WordPress site because cheap shared WordPress hosting can be simpler and cheaper than maintaining a headless CMS + frontend deployment for a small marketing site.

The migration must not produce a rigid PHP theme where every content change needs a developer. The site should be editable through the WordPress block editor / Site Editor, with reusable visual blocks and patterns matching the current design. Paid plugins should not be required. Any bespoke functionality should live in our own theme/plugin code.

Cost note: WordPress can be cheaper if hosted on basic shared hosting with PHP/MySQL, free SSL, and host-level backups. It is not automatically cheaper if using premium managed WordPress hosting or paid plugin subscriptions. This plan avoids paid plugin subscriptions.

## 2. Goals & Non-Goals

- Goals:
  - Port the existing design and routes to WordPress:
    - `/`
    - `/about`
    - `/services`
    - `/testimonials`
    - `/booking`
  - Build a native WordPress block theme using Full Site Editing.
  - Make the header, footer, page templates, page content, and major sections editable in the visual editor.
  - Preserve the current visual language:
    - warm cream/green/orange palette
    - Playfair-style serif headings
    - Jakarta-style sans body text
    - organic image shapes
    - botanical dividers
    - large editorial sections
    - card-based services/testimonials
  - Avoid paid plugins and page builders.
  - Build custom Gutenberg blocks only where core blocks/patterns are not enough.
  - Replace Sanity schemas with WordPress-native content:
    - WordPress pages for page content
    - custom post type for services
    - custom post type for testimonials
    - WordPress Site Editor/global styles for site identity/navigation/footer
    - small custom settings page for booking URL and contact recipient
  - Replace the Next.js contact API with a custom WordPress contact endpoint using `wp_mail()`.
  - Keep Calendly embed support through a custom editable block.
  - Provide import tooling from existing `content/*.json` and `public/images/*`.
  - Keep the current Next/Sanity site intact until WordPress reaches parity.
  - Document editor handoff instructions for non-technical content updates.

- Non-Goals:
  - No headless WordPress + Next.js architecture.
  - No Elementor, Divi, WPBakery, ACF Pro, Gravity Forms, WPForms Pro, or paid SEO/cache plugins.
  - No payment processing or ecommerce.
  - No membership/login area for visitors.
  - No multilingual build unless requested later.
  - No blog migration unless requested; the theme can support posts, but the initial migration focuses on the existing marketing pages.
  - No redesign beyond preserving/improving the existing layout in WordPress.
  - No auto-commit or deployment cutover without review.

## 3. Proposed Architecture

Use a standard self-hosted WordPress architecture:

```text
Browser
  -> WordPress/PHP on shared hosting or VPS
    -> MySQL/MariaDB content database
    -> Custom block theme: sennen
    -> Custom plugin: sennen-core
      -> Custom blocks
      -> Services/testimonials post types
      -> Contact form endpoint
      -> Booking/contact settings
```

### Repository Strategy

Build the WordPress implementation alongside the current Next.js app first:

```text
sennen-life-coaching/
├── app/                         # existing Next.js app; keep during migration
├── components/                  # existing Next.js components; keep during migration
├── sanity/                      # existing Sanity code; source/reference only
├── content/                     # JSON source content for import
├── public/images/               # source images for import
├── docs/
│   └── wordpress-migration-plan.md
└── wordpress/
    ├── docker-compose.yml        # local WordPress + DB dev environment
    ├── .env.example
    ├── README.md
    ├── wp-content/
    │   ├── themes/
    │   │   └── sennen/
    │   └── plugins/
    │       └── sennen-core/
    └── scripts/
        └── import-content.sh
```

Only after WordPress has reached content/design/feature parity should we either:

1. keep the repo as a WordPress theme/plugin repo and archive the Next code, or
2. remove the Next/Sanity files in a final cleanup commit.

This avoids a big-bang rewrite that leaves the site broken mid-migration.

### Theme vs Plugin Split

Use a clean WordPress split:

- `sennen` theme:
  - design system
  - block templates
  - template parts
  - block patterns
  - editor styles
  - frontend styling
  - theme supports

- `sennen-core` plugin:
  - custom post types
  - post meta registration
  - custom blocks with behavior
  - contact form endpoint
  - settings page
  - import helpers / WP-CLI commands

Reason: content types and forms are site functionality, not presentation. If the theme changes later, services/testimonials/contact behavior should remain available.

### Editing Model

Use native WordPress blocks as much as possible:

- Header: Site Logo, Site Title, Navigation, Buttons.
- Footer: Group, Site Title, Paragraph, Social Links.
- Hero sections: Cover/Image/Group/Heading/Button patterns.
- Text/image sections: Group, Columns, Image, Heading, Paragraph.
- Cards: Group patterns plus custom styles.
- FAQs: Details block or custom FAQ pattern using core blocks.

Build custom blocks only for repeated or behavioral pieces:

- `sennen/botanical-divider`
- `sennen/booking-embed`
- `sennen/contact-form`
- `sennen/service-list`
- `sennen/testimonial-list`

This gives visual editor flexibility without recreating a page builder.

### Hosting Approach

Target cheap WordPress hosting with:

- PHP 8.2+
- MySQL/MariaDB
- SSL included
- SFTP/SSH or Git deployment if possible
- daily host-level backups if possible
- staging site if available
- ability to set `DISALLOW_FILE_EDIT` and configure SMTP/mail if needed

Do not depend on hosting-specific APIs in theme/plugin code.

## 4. Component Breakdown

### `sennen` Block Theme

Responsibilities:

- Register a WordPress block theme.
- Define global styles through `theme.json`.
- Mirror the existing design tokens from `app/globals.css`:
  - colors
  - font families
  - typography scale
  - spacing scale
  - button styles
  - card styles
  - organic image shapes
- Provide Full Site Editing templates:
  - `front-page.html`
  - `page.html`
  - `single.html`
  - `archive.html`
  - `404.html`
  - `index.html`
- Provide template parts:
  - `header.html`
  - `footer.html`
- Provide block patterns:
  - Home hero
  - Botanical divider
  - Philosophy text/image section
  - About journey section
  - Philosophy cards grid
  - Services intro section
  - Services grid section
  - Testimonials grid section
  - Booking/contact section
  - FAQ section
  - CTA section
- Provide editor styles so the block editor looks close to the frontend.
- Register block styles for core blocks:
  - organic image shape 1/2/3
  - ambient card
  - label caps
  - primary/secondary buttons
  - textured surface section

Boundaries:

- The theme must not own business data structures.
- The theme must not handle form submission.
- The theme can provide styling and patterns for plugin blocks.

### `sennen-core` Plugin

Responsibilities:

- Register custom post types:
  - `sennen_service`
  - `sennen_testimonial`
- Register REST-visible meta fields:
  - service price
  - service duration
  - service features
  - service sort order
  - testimonial author title
- Register site settings:
  - booking URL
  - contact email recipient
  - optional default contact form inquiry types
- Register custom blocks:
  - `sennen/botanical-divider`
  - `sennen/booking-embed`
  - `sennen/contact-form`
  - `sennen/service-list`
  - `sennen/testimonial-list`
- Provide render callbacks for dynamic blocks.
- Provide the public contact form REST endpoint.
- Provide import tooling for current JSON content.

Boundaries:

- The plugin must sanitize and escape all user-controlled input.
- The plugin must not require paid third-party services.
- The plugin should work with the `sennen` theme, but avoid hard-coding theme internals where possible.

### Services Content

Use `sennen_service` custom posts.

Each service supports:

- title
- editor body/content
- excerpt/short description
- featured image
- price meta
- duration meta
- features meta
- sort order meta

The `/services` page uses `sennen/service-list` to render service cards. The editor can change surrounding copy, headings, card count/order, and layout columns. Individual service cards are edited by opening each service post.

### Testimonials Content

Use `sennen_testimonial` custom posts.

Each testimonial supports:

- title/internal name
- quote in editor or excerpt
- author name/title
- featured image/avatar
- sort order/date

The `/testimonials` page and home testimonial section use `sennen/testimonial-list`.

### Contact Form

Use a custom block and custom REST endpoint.

- No paid form plugin.
- No external email API required by default.
- Uses `wp_mail()` so cheap hosting can send email.
- If host email delivery is poor, configure SMTP at host level or add our own minimal SMTP configuration later.

### Booking Embed

Use a custom block with:

- per-block Calendly URL override, or
- global booking URL from `Settings > Sennen`.

The booking page remains visually editable around the embed.

### SEO Basics

Avoid paid SEO plugins for initial build.

Implement basic SEO in theme/plugin:

- correct document titles via WordPress core
- per-page excerpts where useful
- Open Graph title/description/image fallback
- canonical URLs using WordPress core helpers
- XML sitemap via WordPress core
- robots handled by WordPress core/host

If advanced SEO is needed later, evaluate free plugins separately, but it is not required for launch.

## 5. Data Flow

### Editor Flow

1. Editor logs into WordPress admin.
2. Editor opens Pages, Services, Testimonials, or Appearance → Editor.
3. Editor updates content visually with core blocks, custom blocks, and Sennen patterns.
4. WordPress saves block markup to `post_content`, post meta to `wp_postmeta`, and global styles/templates to the database.
5. Frontend templates render saved blocks and dynamic blocks.

### Visitor Flow

1. Visitor requests `/`, `/about`, `/services`, `/testimonials`, or `/booking`.
2. WordPress resolves the page/template.
3. The `sennen` theme renders block templates and page content.
4. Dynamic plugin blocks query services/testimonials/settings if present.
5. HTML/CSS/JS is served directly from WordPress.

### Services Flow

1. Editor creates/updates a Service post.
2. Service metadata is stored in registered post meta.
3. `sennen/service-list` queries `sennen_service` posts ordered by sort order.
4. The block renders cards matching the current services layout.

### Testimonials Flow

1. Editor creates/updates a Testimonial post.
2. Quote/author/avatar data is stored in post content/meta/featured image.
3. `sennen/testimonial-list` queries `sennen_testimonial` posts.
4. Home and Testimonials pages render testimonial cards.

### Booking Flow

1. Editor sets the global Calendly URL in `Settings > Sennen`, or overrides it in a `sennen/booking-embed` block.
2. Visitor opens `/booking`.
3. The booking block renders the Calendly inline widget.

### Contact Flow

1. Visitor fills the contact form.
2. Frontend JS posts to `/wp-json/sennen/v1/contact` with a nonce and honeypot field.
3. Plugin validates nonce, rate limit, honeypot, field lengths, and email format.
4. Plugin sanitizes input and calls `wp_mail()`.
5. Endpoint returns success or validation error.
6. Block shows success/error feedback.

### Migration Flow

1. Existing `content/*.json` files are treated as the initial source of truth.
2. Import script creates WordPress pages, services, testimonials, and settings.
3. Existing `public/images/*` are imported to the WordPress Media Library.
4. Pages are populated with block pattern markup.
5. Editor reviews and adjusts content in the visual editor.

## 6. Interface Contracts

### Custom Post Type: `sennen_service`

- Public slug: `/services/<service-slug>/` if single service pages are enabled.
- Admin label: Services.
- Supports:
  - `title`
  - `editor`
  - `excerpt`
  - `thumbnail`
  - `page-attributes`
  - `custom-fields` only if needed for debugging; normal editing uses custom UI.
- REST: enabled.

Registered meta:

```php
_sennen_service_price: string
_sennen_service_duration: string
_sennen_service_features: array<string>
_sennen_service_sort_order: integer
```

Validation:

- price: sanitized text, max 80 chars
- duration: sanitized text, max 80 chars
- features: sanitized text array, max 20 items
- sort order: integer

### Custom Post Type: `sennen_testimonial`

- Public slug: `/testimonials/<testimonial-slug>/` if single testimonial pages are enabled.
- Admin label: Testimonials.
- Supports:
  - `title`
  - `editor`
  - `excerpt`
  - `thumbnail`
  - `page-attributes`
- REST: enabled.

Registered meta:

```php
_sennen_testimonial_author_title: string
_sennen_testimonial_sort_order: integer
```

Validation:

- author title: sanitized text, max 120 chars
- sort order: integer

### Settings

Options registered through the WordPress Settings API:

```php
sennen_booking_url: string URL
sennen_contact_email: string email
sennen_default_inquiry_types: array<string>
```

Defaults:

```php
sennen_booking_url = ''
sennen_contact_email = get_option('admin_email')
sennen_default_inquiry_types = [
  'General Question',
  'Bespoke Retreat',
  'Virtual Session',
  'In-Person Session'
]
```

### REST Endpoint: Contact Form

Endpoint:

```http
POST /wp-json/sennen/v1/contact
```

Input:

```json
{
  "name": "string, required, 2-120 chars",
  "email": "string, required, valid email",
  "inquiryType": "string, required, one allowed value",
  "message": "string, required, 10-5000 chars",
  "website": "string, honeypot, must be empty",
  "nonce": "string, required"
}
```

Success response:

```json
{
  "success": true
}
```

Error responses:

```json
{ "success": false, "error": "Invalid nonce" }
{ "success": false, "error": "Please enter your name" }
{ "success": false, "error": "Please enter a valid email" }
{ "success": false, "error": "Please enter a message" }
{ "success": false, "error": "Please wait before sending another message" }
{ "success": false, "error": "Message could not be sent" }
```

Security requirements:

- Verify nonce.
- Check honeypot.
- Rate limit by IP using transients.
- Sanitize all fields.
- Escape all email HTML.
- Prevent mail header injection.
- Do not expose the destination email in frontend markup unless intentionally configured.

### Block: `sennen/booking-embed`

Attributes:

```json
{
  "useGlobalUrl": { "type": "boolean", "default": true },
  "bookingUrl": { "type": "string", "default": "" },
  "minHeight": { "type": "number", "default": 700 }
}
```

Render behavior:

- If `useGlobalUrl` is true, use `sennen_booking_url`.
- If no URL is configured, show an editor/admin-friendly placeholder.
- On frontend, render Calendly inline widget script and container.

### Block: `sennen/contact-form`

Attributes:

```json
{
  "buttonText": { "type": "string", "default": "Send Message" },
  "successMessage": { "type": "string", "default": "Your message has been sent. I look forward to connecting with you soon." },
  "inquiryTypes": { "type": "array", "default": [] }
}
```

Render behavior:

- Render accessible form markup.
- Use global inquiry types when block attribute is empty.
- Enqueue frontend JS only when the block is present.

### Block: `sennen/service-list`

Attributes:

```json
{
  "columns": { "type": "number", "default": 3 },
  "count": { "type": "number", "default": 3 },
  "orderBy": { "type": "string", "default": "sort_order" },
  "showFeatures": { "type": "boolean", "default": true },
  "buttonText": { "type": "string", "default": "Inquire Now" },
  "buttonUrl": { "type": "string", "default": "/booking" }
}
```

Render behavior:

- Query published `sennen_service` posts.
- Sort by `_sennen_service_sort_order`, then title.
- Render cards matching the current services page.

### Block: `sennen/testimonial-list`

Attributes:

```json
{
  "columns": { "type": "number", "default": 3 },
  "count": { "type": "number", "default": 3 },
  "showAvatar": { "type": "boolean", "default": true },
  "source": { "type": "string", "default": "latest" }
}
```

Render behavior:

- Query published `sennen_testimonial` posts.
- Render quote cards with author title and optional avatar.

### Block: `sennen/botanical-divider`

Attributes:

```json
{
  "variant": { "type": "string", "default": "up" },
  "opacity": { "type": "number", "default": 30 }
}
```

Render behavior:

- Render inline SVG divider matching the current design.
- No external assets required.

## 7. File Changes

### Create

- `docs/wordpress-migration-plan.md` — this implementation plan.
- `wordpress/README.md` — WordPress local dev, build, deploy, and editor handoff notes.
- `wordpress/docker-compose.yml` — local WordPress/MySQL development stack.
- `wordpress/.env.example` — local database/admin settings.
- `wordpress/scripts/import-content.sh` — wrapper for WP-CLI import.
- `wordpress/wp-content/themes/sennen/style.css` — theme header.
- `wordpress/wp-content/themes/sennen/functions.php` — theme setup, styles, block styles, pattern categories.
- `wordpress/wp-content/themes/sennen/theme.json` — global design tokens and block defaults.
- `wordpress/wp-content/themes/sennen/assets/css/frontend.css` — frontend CSS not covered by `theme.json`.
- `wordpress/wp-content/themes/sennen/assets/css/editor.css` — editor-only visual parity CSS.
- `wordpress/wp-content/themes/sennen/assets/fonts/` — self-hosted fonts if licensing/source allows.
- `wordpress/wp-content/themes/sennen/templates/front-page.html` — block template for home.
- `wordpress/wp-content/themes/sennen/templates/page.html` — default page template.
- `wordpress/wp-content/themes/sennen/templates/single.html` — default single template.
- `wordpress/wp-content/themes/sennen/templates/archive.html` — default archive template.
- `wordpress/wp-content/themes/sennen/templates/index.html` — fallback template.
- `wordpress/wp-content/themes/sennen/templates/404.html` — not-found template.
- `wordpress/wp-content/themes/sennen/parts/header.html` — editable header template part.
- `wordpress/wp-content/themes/sennen/parts/footer.html` — editable footer template part.
- `wordpress/wp-content/themes/sennen/patterns/home-hero.php` — home hero pattern.
- `wordpress/wp-content/themes/sennen/patterns/philosophy-section.php` — home philosophy section pattern.
- `wordpress/wp-content/themes/sennen/patterns/about-journey-section.php` — about content pattern.
- `wordpress/wp-content/themes/sennen/patterns/philosophy-cards.php` — card grid pattern.
- `wordpress/wp-content/themes/sennen/patterns/services-intro.php` — services intro pattern.
- `wordpress/wp-content/themes/sennen/patterns/booking-contact-section.php` — booking/contact pattern.
- `wordpress/wp-content/themes/sennen/patterns/faq-section.php` — FAQ pattern.
- `wordpress/wp-content/themes/sennen/patterns/cta-section.php` — CTA pattern.
- `wordpress/wp-content/plugins/sennen-core/sennen-core.php` — plugin bootstrap.
- `wordpress/wp-content/plugins/sennen-core/includes/post-types.php` — custom post types.
- `wordpress/wp-content/plugins/sennen-core/includes/meta.php` — registered post meta.
- `wordpress/wp-content/plugins/sennen-core/includes/settings.php` — settings page/options.
- `wordpress/wp-content/plugins/sennen-core/includes/contact.php` — REST contact endpoint and mail handling.
- `wordpress/wp-content/plugins/sennen-core/includes/importer.php` — import helpers or WP-CLI commands.
- `wordpress/wp-content/plugins/sennen-core/src/blocks/booking-embed/` — booking block source.
- `wordpress/wp-content/plugins/sennen-core/src/blocks/contact-form/` — contact form block source.
- `wordpress/wp-content/plugins/sennen-core/src/blocks/service-list/` — service list block source.
- `wordpress/wp-content/plugins/sennen-core/src/blocks/testimonial-list/` — testimonial list block source.
- `wordpress/wp-content/plugins/sennen-core/src/blocks/botanical-divider/` — divider block source.
- `wordpress/wp-content/plugins/sennen-core/build/` — compiled block assets.
- `wordpress/wp-content/plugins/sennen-core/package.json` — block build scripts using `@wordpress/scripts`.
- `wordpress/wp-content/plugins/sennen-core/block-manifest.php` or equivalent block registration generated by build.

### Modify

- `README.md` — update project overview once WordPress is the main implementation.
- `.gitignore` — ignore WordPress local uploads/cache/database artifacts while allowing custom theme/plugin source.
- `.env.example` — either keep Next/Sanity until cutover or add WordPress-specific notes.
- `docs/sennen-life-coaching-plan.md` — optionally add a note that the Sanity/Next plan has been superseded after migration approval.

### Delete After Cutover Only

Do not delete these until the WordPress site has passed QA and the user approves cutover:

- `app/` — Next.js App Router pages/API.
- `components/` — React components.
- `sanity/` — Sanity client/schemas/queries.
- `content/` — Sanity JSON source files; archive first if needed.
- `scripts/seed-sanity*.ts` and `scripts/push-content.ts` — Sanity tooling.
- `next.config.ts` — Next.js config.
- `vercel.json` — Vercel config.
- `package.json` / `package-lock.json` — Next/Sanity dependencies, unless retained only for WordPress block build tooling in a new package structure.
- `tsconfig.json`, `postcss.config.mjs`, `eslint.config.mjs`, `.eslintrc.json` — Next/Tailwind tooling, unless still needed for block JS tooling.

## 8. Implementation Phases

Use one migration branch unless the user explicitly wants separate review branches:

- Branch: `feature/wordpress-port`

Keep commits small and reviewable. Do not remove the existing Next/Sanity app until the final cleanup phase.

### Subagent Code Review Gate

Every phase must end with an isolated code review before the next phase starts:

- Run the phase's automated checks and manual QA first.
- Ask a code-review subagent to review only the changes from that phase.
- Run the review subagent with **GPT-5.5 medium**.
- The subagent should check:
  - correctness against the phase scope
  - WordPress/PHP/block-editor conventions
  - security and escaping/sanitization
  - accessibility regressions
  - editor usability
  - unnecessary scope creep
- Fix all blocking findings in a follow-up commit for that phase.
- Re-run relevant checks after fixes.
- Do not start the next phase until the subagent review is clean or explicitly accepted by the user.

Suggested subagent configuration:

- Agent: `code-review`
- Model: `GPT-5.5 medium`
- Mode: read-only review; do not modify files

Suggested subagent prompt:

```text
Review the changes for Phase <N> of docs/wordpress-migration-plan.md. Use GPT-5.5 medium. Check only this phase's diff against the approved plan. Flag blockers, security issues, WordPress convention problems, editor UX regressions, accessibility issues, and unnecessary scope creep. Do not modify files; return findings grouped by severity with file paths and suggested fixes.
```

### Phase 1 — WordPress Scaffold and Local Environment

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Add `wordpress/docker-compose.yml` and `.env.example` for local WordPress/MySQL.
  - [ ] Add empty `sennen` block theme with `style.css`, `functions.php`, `theme.json`, and starter templates.
  - [ ] Add empty `sennen-core` plugin bootstrap.
  - [ ] Add WordPress `.gitignore` rules for uploads/cache/database artifacts.
  - [ ] Document local setup in `wordpress/README.md`.
- Done when:
  - `docker compose up` starts WordPress locally.
  - The `sennen` theme can be activated.
  - The `sennen-core` plugin can be activated.
  - WordPress admin and frontend load without fatal errors.
- Code review gate:
  - [ ] Run a subagent review for Phase 1 before Phase 2 starts.
  - [ ] Fix or explicitly accept all review findings.

### Phase 2 — Theme Design System and Site Editor Parity

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Port color, typography, spacing, and radius/shadow tokens from `app/globals.css` into `theme.json`.
  - [ ] Add frontend/editor CSS for organic shapes, botanical accents, textured backgrounds, and exact responsive refinements.
  - [ ] Register core block styles for cards, organic images, label caps, and Sennen buttons.
  - [ ] Create editable `header.html` and `footer.html` template parts using core blocks.
  - [ ] Create base templates: `front-page.html`, `page.html`, `single.html`, `archive.html`, `404.html`, `index.html`.
  - [ ] Add initial pattern category: `Sennen Sections`.
- Done when:
  - The Site Editor can visually edit header/footer/templates.
  - Global Styles exposes the Sennen palette and typography.
  - Editor view reasonably matches frontend styling.
  - A blank page can be built using core blocks and Sennen styles.
- Code review gate:
  - [ ] Run a subagent review for Phase 2 before Phase 3 starts.
  - [ ] Fix or explicitly accept all review findings.

### Phase 3 — Patterns for Current Page Layouts

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Create home hero pattern matching current `app/page.tsx` hero.
  - [ ] Create botanical divider pattern/block placeholder.
  - [ ] Create home philosophy text/image pattern.
  - [ ] Create about hero and journey patterns.
  - [ ] Create philosophy cards grid pattern.
  - [ ] Create services intro and philosophy patterns.
  - [ ] Create booking/contact/FAQ patterns.
  - [ ] Create CTA patterns.
- Done when:
  - A content editor can insert all major site sections from the block inserter.
  - Sections can be reordered, duplicated, edited, and removed without touching code.
  - Patterns preserve the existing visual design closely on desktop and mobile.
- Code review gate:
  - [ ] Run a subagent review for Phase 3 before Phase 4 starts.
  - [ ] Fix or explicitly accept all review findings.

### Phase 4 — Custom Content Types and Dynamic Blocks

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Register `sennen_service` custom post type.
  - [ ] Register service meta fields and editor UI.
  - [ ] Register `sennen_testimonial` custom post type.
  - [ ] Register testimonial meta fields and editor UI.
  - [ ] Add `sennen/service-list` block with editor controls and server-side render.
  - [ ] Add `sennen/testimonial-list` block with editor controls and server-side render.
  - [ ] Add `sennen/botanical-divider` block.
- Done when:
  - Services can be created/edited in WordPress admin.
  - Testimonials can be created/edited in WordPress admin.
  - Services page can render service cards dynamically.
  - Home and testimonials pages can render testimonial cards dynamically.
  - Dynamic blocks preview correctly in the block editor and frontend.
- Code review gate:
  - [ ] Run a subagent review for Phase 4 before Phase 5 starts.
  - [ ] Fix or explicitly accept all review findings.

### Phase 5 — Booking, Contact, and Settings

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Add `Settings > Sennen` page for booking URL, contact email, and default inquiry types.
  - [ ] Add `sennen/booking-embed` block.
  - [ ] Add `sennen/contact-form` block frontend and editor UI.
  - [ ] Add `/wp-json/sennen/v1/contact` endpoint.
  - [ ] Add nonce, honeypot, sanitization, email validation, and IP rate limiting.
  - [ ] Add local email testing notes/tooling.
- Done when:
  - Booking page displays Calendly when a URL is configured.
  - Missing booking URL shows a helpful editor/admin placeholder.
  - Contact form validates client-side and server-side.
  - Contact form sends a test email through local/hosting mail.
  - Spam protection basics are in place without paid plugins.
- Code review gate:
  - [ ] Run a subagent review for Phase 5 before Phase 6 starts.
  - [ ] Fix or explicitly accept all review findings.

### Phase 6 — Content and Media Migration

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Add importer for `content/site-settings.json`.
  - [ ] Add importer for `content/home.json`, `content/about.json`, and `content/booking.json` into WordPress pages with block markup.
  - [ ] Add importer for `content/services.json` into `sennen_service` posts.
  - [ ] Add importer for `content/testimonials.json` into `sennen_testimonial` posts.
  - [ ] Import `public/images/*` into the Media Library and attach featured images where known.
  - [ ] Add a dry-run mode so import output can be reviewed before writing.
  - [ ] Document how to rerun import safely on a fresh WordPress install.
- Done when:
  - WordPress contains Home, About, Services, Testimonials, and Booking pages.
  - Existing services/testimonials are present as WordPress content.
  - Images are in the Media Library.
  - Navigation points to the correct pages.
  - The old Sanity JSON is no longer needed for live editing.
- Code review gate:
  - [ ] Run a subagent review for Phase 6 before Phase 7 starts.
  - [ ] Fix or explicitly accept all review findings.

### Phase 7 — SEO, Performance, Accessibility, and Security Pass

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Add basic Open Graph/meta output without paid SEO plugins.
  - [ ] Confirm WordPress core sitemap is enabled and correct.
  - [ ] Optimize frontend assets so block JS/CSS loads only when needed.
  - [ ] Add responsive fixes for all pages.
  - [ ] Add accessibility fixes for keyboard navigation, focus states, labels, landmarks, and image alt text.
  - [ ] Add hardening recommendations to `wordpress/README.md`.
- Done when:
  - All public pages pass manual desktop/mobile checks.
  - Lighthouse is acceptable for Performance, Accessibility, Best Practices, and SEO.
  - No obvious PHP warnings/notices in debug logs.
  - Contact form endpoint cannot be spammed trivially.
  - Admin/editor docs explain how to update content safely.
- Code review gate:
  - [ ] Run a subagent review for Phase 7 before Phase 8 starts.
  - [ ] Fix or explicitly accept all review findings.

### Phase 8 — Deployment and Cutover

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Choose target WordPress host and confirm PHP/MySQL versions.
  - [ ] Deploy theme and plugin to staging/production WordPress.
  - [ ] Run import on staging/production.
  - [ ] Configure permalinks to `/%postname%/`.
  - [ ] Configure domain and SSL.
  - [ ] Verify redirects/routes from the existing site.
  - [ ] Configure backup policy.
  - [ ] Smoke test live contact form and booking embed.
- Done when:
  - Production WordPress site is live.
  - All existing URLs resolve correctly.
  - Editor can log in and update content through the block editor.
  - Contact form sends to the chosen email.
  - Booking URL works.
  - User approves final cutover.
- Code review gate:
  - [ ] Run a subagent review for Phase 8 before Phase 9 starts.
  - [ ] Fix or explicitly accept all review findings.

### Phase 9 — Retire Sanity/Next.js

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Archive/export final Sanity content and assets if needed.
  - [ ] Remove Sanity/Next/Vercel files from the repo, or move them to an archive branch/tag.
  - [ ] Update root `README.md` so WordPress is the source of truth.
  - [ ] Remove unused environment variables and deployment docs.
- Done when:
  - Repo no longer presents the Next/Sanity app as the active implementation.
  - The WordPress theme/plugin/local dev docs are the clear entry point.
  - Old CMS/deployment services can be safely cancelled or left inactive.
- Code review gate:
  - [ ] Run a final subagent review for Phase 9 and the overall migration cleanup.
  - [ ] Fix or explicitly accept all review findings before merging/cutover cleanup.

## 9. Testing Strategy

### Automated / Command Checks

Run during development:

```bash
php -v
find wordpress/wp-content/themes/sennen wordpress/wp-content/plugins/sennen-core -name '*.php' -print0 | xargs -0 -n1 php -l
```

For custom block builds:

```bash
cd wordpress/wp-content/plugins/sennen-core
npm install
npm run build
```

If test tooling is added:

```bash
npm run lint
npm run test
```

### Manual WordPress QA

- Local environment:
  - WordPress boots cleanly.
  - Theme activates.
  - Plugin activates.
  - No PHP fatal errors.
- Site Editor:
  - Header editable.
  - Footer editable.
  - Global colors/typography editable.
  - Templates editable.
- Page editor:
  - Patterns appear in inserter.
  - Patterns can be inserted, edited, reordered, duplicated, and removed.
  - Custom blocks preview in editor.
- Frontend pages:
  - `/`
  - `/about`
  - `/services`
  - `/testimonials`
  - `/booking`
  - `404`
- Services:
  - Create/edit/delete service.
  - Set price/duration/features/order.
  - Service list updates correctly.
- Testimonials:
  - Create/edit/delete testimonial.
  - Set author title/avatar/order.
  - Testimonial lists update correctly.
- Booking:
  - Empty booking URL shows placeholder.
  - Valid Calendly URL loads widget.
- Contact:
  - Empty form shows validation errors.
  - Invalid email rejected.
  - Honeypot submission rejected.
  - Repeated submissions rate-limited.
  - Valid submission sends email.
  - Email content is escaped and readable.
- Content migration:
  - Dry-run output matches expected content.
  - Import creates correct pages/posts/media.
  - Rerun does not duplicate content unexpectedly.

### Visual Regression / Parity Checks

Compare WordPress against current Next/Sanity site for:

- hero section layout
- typography scale
- color palette
- nav behavior
- footer layout
- services cards
- testimonial cards
- booking/contact page
- mobile layout at 375px
- tablet layout around 768px
- desktop layout around 1440px

### Accessibility Checks

- All form fields have labels.
- Buttons and links have visible focus states.
- Header navigation is keyboard usable.
- Color contrast is acceptable.
- Images have meaningful alt text or empty alt where decorative.
- Dynamic form status messages use `aria-live`.
- Heading hierarchy is logical.

### Performance Checks

- Only load custom block frontend scripts when the block appears.
- Use WebP/optimized images through WordPress image sizes.
- Avoid large global JS bundles.
- Confirm no external page-builder assets.
- Enable host/server caching if available.
- Confirm no uncached expensive queries on dynamic blocks.

### Deployment Checks

- SSL active.
- Permalinks saved.
- Sitemap available.
- Robots not blocking production.
- Contact email works from production.
- Admin account secured.
- Backups configured.
- Old Vercel/Sanity services not switched off until production WordPress is verified.

## 10. Security Implications

WordPress increases the maintenance/security surface compared with a static/ISR Next.js frontend. The plan reduces risk by minimizing plugins and owning the custom code.

### Authentication / Authorization

- Public visitors can only read public pages/posts and submit the contact form.
- WordPress admins/editors manage content through WP admin.
- Settings page requires `manage_options`.
- Service/testimonial editing uses WordPress capabilities.
- Do not create shared admin accounts.
- Use strong passwords and least-privilege editor accounts.

### Input Validation

User-controlled inputs:

- contact form fields
- block attributes saved by editors
- post meta fields
- settings fields

Requirements:

- Sanitize on save.
- Escape on output.
- Validate email/URL fields.
- Restrict allowed inquiry types.
- Cap string lengths.
- Use nonces for form submissions and settings.

### Contact Form Risks

Risks:

- spam
- mail header injection
- XSS in email body
- endpoint abuse

Mitigations:

- nonce check
- honeypot field
- IP-based transient rate limit
- server-side validation
- `sanitize_text_field`, `sanitize_email`, `esc_html`, `wp_kses_post` only where appropriate
- never place raw user input into headers
- generic errors to avoid leaking internals

### WordPress Hardening

Recommended production configuration:

```php
define('DISALLOW_FILE_EDIT', true);
define('WP_DEBUG_DISPLAY', false);
```

Also:

- Keep WordPress core, theme, and plugin updated.
- Remove unused default themes/plugins where safe.
- Use host-level malware scanning/backups if included.
- Use SFTP/SSH, not insecure FTP.
- Set sensible file permissions.
- Do not commit production credentials.

### Sensitive Data

- Contact recipient email stored in WordPress options.
- No payment/card data handled.
- No health records or private coaching notes stored by this site.
- Contact submissions are sent by email and not stored unless explicitly added later.

## 11. Risks & Tradeoffs

| Risk | Mitigation |
|---|---|
| Cheap WordPress hosting may be slower than Vercel. | Keep theme lightweight, avoid page builders, use host caching, optimize images. |
| WordPress needs ongoing updates/security maintenance. | Minimize plugins, document update process, use host backups, harden config. |
| Building custom blocks costs more upfront than buying plugins. | Limit custom blocks to behavior we actually need; use core blocks/patterns for layout. |
| Visual editor parity can take time. | Add editor CSS and patterns early; test editing UX as a deliverable, not an afterthought. |
| WordPress block markup can be brittle if heavily customized. | Prefer core blocks/patterns and dynamic blocks with stable render callbacks. |
| Services/testimonials as CPTs are less directly WYSIWYG than manual cards. | Provide dynamic list blocks with clear editor previews and simple edit links/instructions. |
| `wp_mail()` deliverability depends on host mail. | Test on production; if needed, configure host SMTP or add a minimal custom SMTP configuration without paid plugins. |
| Sanity may contain newer content than `content/*.json`. | Before final import, export or manually verify current Sanity content. |
| Existing Next/Sanity code removal could lose reference material. | Archive via branch/tag before cleanup. |
| No paid SEO/form/cache plugins means fewer admin conveniences. | Implement launch-critical basics ourselves; evaluate free plugins only if a real need appears. |

## 12. Open Questions

Implementation can start with these defaults; unresolved items are either deployment-only or accepted as risks.

1. Which WordPress host will be used?
   - Default for development: local Docker WordPress.
   - Accepted risk: final deployment steps wait until a host is chosen.

2. Should we use WordPress.org self-hosted or WordPress.com?
   - Default decision: WordPress.org self-hosted, because custom themes/plugins and cheap hosting are required.

3. Is Sanity currently ahead of `content/*.json`?
   - Default decision: use `content/*.json` for initial import.
   - Accepted risk: before cutover, verify/export live Sanity content so no edits are lost.

4. What are the final booking URL, contact email, and social URLs?
   - Default decision: keep placeholders/settings blank locally.
   - Accepted risk: production launch is blocked until these values are supplied.

5. Do we need a blog at launch?
   - Default decision: no. WordPress posts can remain available, but no blog section is in launch scope.

6. Do we want service/testimonial single pages public?
   - Default decision: register CPTs publicly but do not link single pages in nav. The Services and Testimonials pages are the main user-facing views.

7. Should contact submissions be stored in WordPress admin?
   - Default decision: no, email only. This avoids storing personal messages/PII in the database.
   - Accepted risk: if email delivery fails, there is no database copy unless we add explicit storage later.
