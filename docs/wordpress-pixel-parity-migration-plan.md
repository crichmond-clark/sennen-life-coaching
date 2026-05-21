# WordPress Pixel-Parity Migration Plan

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

The first WordPress migration successfully created a working local WordPress environment, block theme, custom plugin, content importer, custom content types, booking embed, contact form, and dynamic service/testimonial blocks. However, the current WordPress pages are only visually similar to the existing Next.js frontend.

The original intent is stricter: the public WordPress frontend should match the existing Next.js frontend as closely as possible, page-by-page and breakpoint-by-breakpoint, while keeping WordPress as the CMS for the client.

The gap exists because the current WordPress implementation uses a mixture of core Gutenberg layout blocks, theme approximations, and dynamic custom blocks. Gutenberg adds its own wrappers, layout styles, margins, and editor-oriented abstractions. That makes exact parity difficult.

To reach pixel parity, the Next.js frontend must become the source of truth. WordPress should render purpose-built Sennen section blocks whose frontend HTML, CSS classes, images, icons, spacing, typography, transitions, and responsive behavior mirror the existing React/Tailwind components.

## 2. Goals & Non-Goals

- Goals:
  - Match the existing Next.js public frontend for these routes:
    - `/`
    - `/about`
    - `/services`
    - `/testimonials`
    - `/booking`
  - Match the existing layout, typography, color palette, spacing, section heights, cards, organic image shapes, hover states, hero overlays, botanical dividers, header, footer, mobile navigation, booking placeholder, and contact form styling.
  - Keep WordPress as the CMS/admin surface.
  - Keep services and testimonials editable as WordPress custom post types.
  - Keep booking URL/contact email editable through Sennen settings.
  - Keep page copy editable through locked custom section blocks rather than generic freeform Gutenberg layouts.
  - Avoid paid plugins and page builders.
  - Keep the existing Next.js app available as the visual reference until WordPress parity is approved.
  - Add automated visual regression checks so parity can be measured instead of guessed.
  - Preserve the existing deployment goal: self-hosted WordPress on the VPS/shared WordPress-compatible stack.
  - Run a subagent code review gate after every implementation phase using GPT-5.5 medium.

- Non-Goals:
  - No redesign.
  - No headless WordPress + Next.js frontend. That would trivially preserve the Next UI, but it would not satisfy the simpler WordPress hosting/admin goal.
  - No freeform page-builder experience for the exact-layout pages. Freeform editing and pixel parity conflict.
  - No requirement for the WordPress block editor canvas to be pixel-perfect. The public frontend is the parity target; the editor needs clear server-side previews and safe controls.
  - No exact match while logged in with the WordPress admin bar visible.
  - No ecommerce, memberships, or new visitor-facing functionality.
  - No automatic production cutover without manual review.

## 3. Proposed Architecture

### Core Decision

Replace generic Gutenberg page layouts with a small set of locked, purpose-built Sennen section blocks. Each block renders frontend HTML modeled directly from the equivalent Next.js JSX.

The public frontend becomes:

```text
WordPress template shell
  -> exact Sennen nav renderer
  -> page content made of exact Sennen section blocks
  -> exact Sennen footer renderer
  -> compiled parity CSS from the existing Tailwind design system
  -> minimal interaction JS for nav, mobile menu, booking, and form behavior
```

### Source of Truth

The source files for parity are:

- `app/layout.tsx`
- `components/NavBar.tsx`
- `components/Footer.tsx`
- `components/Testimonials.tsx`
- `components/BookingEmbed.tsx`
- `components/ContactForm.tsx`
- `app/page.tsx`
- `app/about/page.tsx`
- `app/services/page.tsx`
- `app/testimonials/page.tsx`
- `app/booking/page.tsx`
- `app/globals.css`
- `public/images/*`

When there is disagreement between the current WordPress output and these files, the Next.js files win.

### CSS Strategy

Use the existing Tailwind v4 design system instead of hand-approximating styles.

Create a WordPress parity CSS build that:

- starts from the same theme definitions in `app/globals.css`
- scans the original Next.js TSX files and the new WordPress PHP/JS render files
- emits a compiled CSS asset for the WordPress theme
- includes the same utility classes used by the Next.js frontend
- keeps WordPress-specific reset/override CSS small and explicit

The goal is for WordPress renderers to use the same class strings as the React components wherever practical, e.g.:

```html
<section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden -mt-[72px]">
```

rather than an approximation like:

```html
<div class="wp-block-group alignfull sennen-cover-hero sennen-home-hero">
```

### Editing Model

Use locked section blocks rather than open-ended Gutenberg columns/groups.

The client can edit:

- page headings/subtitles/body copy through block attributes
- service packages through the Services content type
- testimonials through the Testimonials content type
- booking URL and contact email through Sennen settings
- social links/site name/tagline through site settings/options

The client should not freely rearrange exact-layout pages unless they accept that parity may be broken. The imported pages should have their layout locked.

### Theme/Plugin Split

- `sennen` theme:
  - compiled parity CSS
  - exact global shell styling
  - exact header/nav renderer
  - exact footer renderer
  - small frontend JS for navigation/mobile interactions
  - block theme templates that keep WordPress wrappers minimal
  - editor styling for reasonable previews

- `sennen-core` plugin:
  - services/testimonials content types
  - booking/contact settings
  - contact REST endpoint
  - custom section blocks for page content
  - dynamic service/testimonial lists with exact card markup
  - importer that writes locked exact section blocks into pages

### Visual Regression Strategy

Run both sites locally in the same browser engine:

- Next.js reference: `http://localhost:3000`
- WordPress candidate: `http://localhost:8080`

Capture screenshots for each route at agreed viewport widths and compare them with Pixelmatch/Playwright.

Target viewports:

- mobile: `375x900`
- tablet: `768x1024`
- desktop: `1440x1100`
- large desktop: `1920x1200`

Acceptance threshold:

- Aim for less than `0.5%` different pixels per screenshot after masking unavoidable third-party widgets.
- Calendly iframe content should be masked or tested in placeholder mode because third-party iframe rendering is outside our control.
- Minor font anti-aliasing differences are accepted only when layout, spacing, color, and content position match.

## 4. Component Breakdown

### Exact Theme Shell

Responsibilities:

- Render `<body>`/main layout equivalent to `app/layout.tsx`:
  - background color
  - text color
  - body typography
  - `overflow-x-hidden`
  - selection styles
  - min-height flex column behavior
- Render nav equivalent to `components/NavBar.tsx`:
  - fixed top nav
  - transparent/unscrolled state
  - scrolled state after `20px`
  - active link styling
  - desktop nav links
  - CTA button
  - mobile menu toggle
  - mobile menu panel
  - inline SVG menu/close icons
- Render footer equivalent to `components/Footer.tsx`:
  - surface-container background
  - top border
  - rounded top corners
  - brand/tagline/copyright
  - social icons

### Exact Section Blocks

Create/refactor custom blocks to render exact frontend sections.

Home:

- `sennen/home-hero`
- `sennen/botanical-divider`
- `sennen/home-philosophy`
- `sennen/home-testimonials-section`

About:

- `sennen/about-hero`
- `sennen/about-journey-sections`
- `sennen/about-philosophy-cards`
- `sennen/about-gallery`

Services:

- `sennen/texture-hero`
- `sennen/services-philosophy`
- `sennen/service-cards`
- `sennen/bespoke-cta`

Testimonials:

- `sennen/testimonials-hero`
- `sennen/testimonial-cards`

Booking:

- `sennen/booking-hero`
- `sennen/booking-contact-grid`
- `sennen/booking-embed`
- `sennen/contact-form`

Some of these may internally share render helpers, but the block surface should stay understandable in the editor.

### Render Helpers

Create shared PHP helpers for repeated exact markup:

- `sennen_core_render_svg_icon( string $name ): string`
- `sennen_core_image_url( string $filename ): string`
- `sennen_core_render_botanical_svg( string $variant ): string`
- `sennen_core_render_label( string $text, string $color_class ): string`
- `sennen_core_render_link_button( string $url, string $label, string $class ): string`
- `sennen_core_extract_portable_text( array $blocks ): array`
- `sennen_core_get_site_setting( string $key, mixed $fallback ): mixed`

### Editor Experience

For each exact block:

- register it in PHP with `block.json`
- register it in editor JavaScript so the editor does not show unsupported block warnings
- use `ServerSideRender` for previews
- expose inspector controls for copy/image/settings only
- avoid layout controls that would break parity
- mark exact page templates as locked after import

### Importer

The importer must output exact section blocks, not core layout blocks.

Example imported Home page structure:

```html
<!-- wp:sennen/home-hero {"heading":"Rooted in Grace","subtitle":"...","ctaText":"Begin Your Journey"} /-->
<!-- wp:sennen/botanical-divider {"variant":"up","opacity":30} /-->
<!-- wp:sennen/home-philosophy {"heading":"The Art of Slowing Down."} /-->
<!-- wp:sennen/home-testimonials-section {"count":3} /-->
```

Existing imported WordPress pages should be safely overwritten by the importer when explicitly requested.

## 5. Data Flow

### Import Flow

1. `content/*.json` remains the migration input.
2. WP-CLI command reads JSON files from `/var/www/html/content`.
3. Services are upserted into `sennen_service` posts.
4. Testimonials are upserted into `sennen_testimonial` posts.
5. Page JSON is converted into exact Sennen section block comments.
6. Existing matching pages are updated by slug.
7. Page templates are locked to preserve layout parity.
8. Home is assigned as the front page.

### Frontend Render Flow

1. Visitor requests a route.
2. WordPress resolves the page.
3. Theme renders exact nav.
4. WordPress renders the page's locked section blocks.
5. Dynamic section blocks query content/settings as needed.
6. Theme renders exact footer.
7. Parity CSS and interaction JS apply the same visual behavior as Next.js.

### Editing Flow

1. Client edits a section block's text/settings or edits Services/Testimonials CPT entries.
2. The editor shows a server-rendered preview.
3. The public frontend renders the exact section template with updated content.
4. Layout remains locked unless a developer intentionally changes templates/blocks.

### Visual Regression Flow

1. Start Next.js reference site.
2. Start WordPress candidate site.
3. Playwright opens both sites using the same browser engine.
4. Screenshots are captured per route/viewport.
5. Pixel diff is generated.
6. Diffs above threshold become implementation tasks.

## 6. Interface Contracts

### WP-CLI Import Command

- Command: `wp sennen import`
- Input:
  - `--dry-run` optional; reads and reports without writes
  - `--force-layout` optional; overwrites existing page block layouts with exact locked section blocks
- Output:
  - logs imported settings, pages, services, testimonials
  - returns non-zero on malformed JSON, missing required files, or failed writes
- Error cases:
  - missing content file
  - invalid JSON
  - invalid email/URL settings
  - failed post insert/update

### Exact Section Block Contract

All custom section blocks must follow this contract:

- `block.json` defines:
  - stable `name`
  - readable `title`
  - `category: sennen-sections`
  - explicit attributes
  - `render` callback file
  - editor script support
- PHP render callback:
  - escapes all dynamic content
  - emits the exact frontend section markup
  - avoids unnecessary WordPress layout wrappers
  - returns/echoes consistently according to `render_block` expectations
- Editor registration:
  - registers the same block name client-side
  - uses server-side preview
  - does not expose destructive layout controls

### Block Attribute Sketch

- `sennen/home-hero`
  - `label: string`
  - `heading: string`
  - `subtitle: string`
  - `ctaText: string`
  - `ctaUrl: string`
  - `imageUrl: string`
- `sennen/home-philosophy`
  - `heading: string`
  - `body: array|string`
  - `imageUrl: string`
  - `ctaText: string`
  - `ctaUrl: string`
- `sennen/about-hero`
  - `label: string`
  - `heading: string`
  - `subtitle: string`
  - `imageUrl: string`
- `sennen/about-journey-sections`
  - `sections: array<{ heading: string, body: string, imageUrl: string }>`
- `sennen/service-cards`
  - `count: number`
  - `buttonText: string`
  - `buttonUrl: string`
- `sennen/testimonial-cards`
  - `count: number`
  - `columns: number`
  - `showAvatar: boolean`
- `sennen/booking-contact-grid`
  - `scheduleHeading: string`
  - `contactHeading: string`
  - `faqHeading: string`
  - `faqs: array<{ question: string, answer: string }>`

### Frontend JS Contract

- Nav script:
  - adds/removes scrolled class after `window.scrollY > 20`
  - toggles mobile menu state
  - closes mobile menu after link click
- Contact form script:
  - validates the same fields as the Next component
  - posts to the existing WordPress REST endpoint
  - displays success/error states with exact visual classes
- Booking script:
  - loads Calendly widget only when a booking URL exists
  - shows exact placeholder when empty

### Visual Test Scripts

- `npm run visual:baseline` captures Next.js screenshots.
- `npm run visual:wordpress` captures WordPress screenshots.
- `npm run visual:compare` compares current WordPress against baseline.
- `npm run visual:update` refreshes approved baselines intentionally.

## 7. File Changes

- Create:
  - `docs/wordpress-pixel-parity-migration-plan.md` — this plan.
  - `wordpress/wp-content/themes/sennen/assets/css/src/parity.css` — Tailwind parity CSS source.
  - `wordpress/wp-content/themes/sennen/assets/css/sennen-parity.css` — compiled parity CSS.
  - `wordpress/wp-content/themes/sennen/assets/js/sennen-frontend.js` — nav/mobile interaction parity.
  - `wordpress/wp-content/themes/sennen/includes/render-shell.php` — exact nav/footer render helpers.
  - `wordpress/wp-content/plugins/sennen-core/includes/render-helpers.php` — shared section render helpers.
  - `wordpress/wp-content/plugins/sennen-core/src/blocks/home-hero/` — exact Home hero block.
  - `wordpress/wp-content/plugins/sennen-core/src/blocks/home-philosophy/` — exact Home philosophy block.
  - `wordpress/wp-content/plugins/sennen-core/src/blocks/home-testimonials-section/` — exact Home testimonials section.
  - `wordpress/wp-content/plugins/sennen-core/src/blocks/about-hero/` — exact About hero block.
  - `wordpress/wp-content/plugins/sennen-core/src/blocks/about-journey-sections/` — exact About journey block.
  - `wordpress/wp-content/plugins/sennen-core/src/blocks/about-philosophy-cards/` — exact About philosophy cards block.
  - `wordpress/wp-content/plugins/sennen-core/src/blocks/about-gallery/` — exact About gallery block.
  - `wordpress/wp-content/plugins/sennen-core/src/blocks/texture-hero/` — exact reusable textured hero block.
  - `wordpress/wp-content/plugins/sennen-core/src/blocks/services-philosophy/` — exact Services philosophy section.
  - `wordpress/wp-content/plugins/sennen-core/src/blocks/bespoke-cta/` — exact Services CTA.
  - `tests/visual/sennen-visual.spec.ts` — screenshot capture/compare tests.
  - `tests/visual/compare.ts` — Pixelmatch comparison helper if Playwright snapshots alone are insufficient.

- Modify:
  - `package.json` — add visual test/build scripts and any required dev dependencies.
  - `wordpress/wp-content/themes/sennen/functions.php` — enqueue parity CSS/JS and exact shell helpers.
  - `wordpress/wp-content/themes/sennen/templates/*.html` — reduce wrappers and use exact header/footer/content shell.
  - `wordpress/wp-content/themes/sennen/parts/header.html` — replace generic block navigation with exact Sennen nav renderer.
  - `wordpress/wp-content/themes/sennen/parts/footer.html` — replace generic footer blocks with exact Sennen footer renderer.
  - `wordpress/wp-content/themes/sennen/assets/css/frontend.css` — remove approximation rules once parity CSS owns frontend layout; keep only WordPress-specific fixes.
  - `wordpress/wp-content/themes/sennen/assets/css/editor.css` — make exact blocks readable in editor without trying to make the editor canvas the source of truth.
  - `wordpress/wp-content/plugins/sennen-core/includes/blocks.php` — register all new exact section blocks and editor scripts.
  - `wordpress/wp-content/plugins/sennen-core/assets/js/editor-blocks.js` — add editor registration/controls for all exact section blocks.
  - `wordpress/wp-content/plugins/sennen-core/includes/importer.php` — output exact section blocks and support `--force-layout`.
  - Existing block renderers for `service-list`, `testimonial-list`, `booking-embed`, `contact-form`, `botanical-divider` — refactor to exact Next.js markup/classes or replace with new exact blocks.
  - `wordpress/README.md` — document parity workflow, import workflow, visual checks, and editor handoff.

- Delete:
  - No immediate deletes required.
  - Old block patterns can remain temporarily for reference, but exact imported pages should stop using them. After parity approval, unused patterns can be removed in a cleanup commit if they confuse the editor.

## 8. Implementation Phases

Use the current branch unless the user requests a separate review branch:

- Branch: `feature/wordpress-port`

Each phase ends with a code review gate:

```text
Run subagent code-review using GPT-5.5 medium.
Review scope: changes since previous phase commit.
Check: parity plan compliance, escaping/security, editor compatibility, no unrelated refactors, and test evidence.
Do not proceed to the next phase until review findings are addressed or explicitly accepted.
```

### Phase 0 — Stabilize Current WordPress Port

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Commit current fixes for WP-CLI, content mounts, meta schema, `.htaccess`, importer corrections, block render fixes, and editor block registration.
  - [ ] Confirm current pages render and editor no longer reports unsupported Sennen blocks.
- Done when:
  - `docker compose` WordPress environment runs.
  - `wp sennen import --dry-run --allow-root` succeeds.
  - Home, About, Services, Testimonials, and Booking return HTTP 200.
  - Existing custom blocks are registered both server-side and editor-side.
  - GPT-5.5 medium subagent review gate passes.

### Phase 1 — Establish Visual Baselines

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Add Playwright visual test harness.
  - [ ] Add scripts to capture Next.js reference screenshots.
  - [ ] Add scripts to capture WordPress candidate screenshots.
  - [ ] Add diff output directory ignored by git unless baselines are intentionally committed.
- Done when:
  - Next.js screenshots can be captured for all target routes/viewports.
  - WordPress screenshots can be captured for all target routes/viewports.
  - Current diffs are visible and documented.
  - GPT-5.5 medium subagent review gate passes.

### Phase 2 — Port the Exact Design System

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Add Tailwind parity CSS source/build for WordPress.
  - [ ] Compile a WordPress CSS asset from the same `app/globals.css` tokens and exact class usage.
  - [ ] Align font loading with the Next.js frontend as closely as possible.
  - [ ] Add minimal WordPress reset rules to neutralize unwanted block margins/wrappers.
- Done when:
  - WordPress can enqueue the compiled parity CSS.
  - Core typography/color/spacing utility classes used by the Next frontend work in WordPress.
  - Global body/main/selection styles match the Next layout shell.
  - GPT-5.5 medium subagent review gate passes.

### Phase 3 — Exact Header and Footer

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Replace generic WordPress header with exact Sennen nav markup.
  - [ ] Add scroll-state and mobile-menu JS matching `NavBar.tsx` behavior.
  - [ ] Replace generic WordPress footer with exact Sennen footer markup/icons.
  - [ ] Keep site name/tagline/social links editable through settings/options.
- Done when:
  - Header/footer visually match Next.js at desktop and mobile widths.
  - Active nav state, CTA, mobile menu, and scroll-state behavior work.
  - Visual diffs for header/footer are under threshold or documented as accepted anti-aliasing differences.
  - GPT-5.5 medium subagent review gate passes.

### Phase 4 — Exact Home Page Sections

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Add exact `sennen/home-hero` renderer.
  - [ ] Refactor `sennen/botanical-divider` to exact SVG/spacing.
  - [ ] Add exact `sennen/home-philosophy` renderer.
  - [ ] Add exact `sennen/home-testimonials-section` renderer.
  - [ ] Update importer to generate exact Home blocks.
- Done when:
  - WordPress Home page matches Next.js Home page across target viewports.
  - Home content remains editable through block attributes/settings/CPTs.
  - Visual diff for Home is under the agreed threshold, excluding known third-party/runtime differences.
  - GPT-5.5 medium subagent review gate passes.

### Phase 5 — Exact About Page Sections

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Add exact `sennen/about-hero` renderer.
  - [ ] Add exact `sennen/about-journey-sections` renderer with alternating layout.
  - [ ] Add exact `sennen/about-philosophy-cards` renderer with icon parity.
  - [ ] Add exact `sennen/about-gallery` renderer.
  - [ ] Update importer to generate exact About blocks.
- Done when:
  - WordPress About page matches Next.js About page across target viewports.
  - Journey sections and philosophy cards remain editable.
  - Visual diff for About is under threshold.
  - GPT-5.5 medium subagent review gate passes.

### Phase 6 — Exact Services and Testimonials Pages

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Add/refactor exact textured hero block for Services and Testimonials.
  - [ ] Add exact Services philosophy section.
  - [ ] Refactor service cards to match `app/services/page.tsx` markup/classes.
  - [ ] Add exact bespoke CTA.
  - [ ] Refactor testimonial cards to match `components/Testimonials.tsx` and `app/testimonials/page.tsx`.
  - [ ] Update importer to generate exact Services and Testimonials blocks.
- Done when:
  - WordPress Services page matches Next.js Services page across target viewports.
  - WordPress Testimonials page matches Next.js Testimonials page across target viewports.
  - Service/testimonial content remains editable through CPTs.
  - Visual diffs for Services and Testimonials are under threshold.
  - GPT-5.5 medium subagent review gate passes.

### Phase 7 — Exact Booking Page, Booking Embed, and Contact Form

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Add exact `sennen/booking-hero` renderer.
  - [ ] Add exact `sennen/booking-contact-grid` renderer.
  - [ ] Refactor booking placeholder/Calendly embed to match `BookingEmbed.tsx`.
  - [ ] Refactor contact form markup, validation states, and button/icon to match `ContactForm.tsx`.
  - [ ] Update importer to generate exact Booking blocks.
- Done when:
  - WordPress Booking page matches Next.js Booking page across target viewports.
  - Contact form validation/submission works.
  - Empty booking URL placeholder matches the Next placeholder.
  - Calendly iframe is loaded only when configured and is masked/excluded from pixel diff if necessary.
  - GPT-5.5 medium subagent review gate passes.

### Phase 8 — Lock Layouts and Harden Editor Experience

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Register editor-side JS for all exact blocks.
  - [ ] Add clear inspector controls for editable copy/settings.
  - [ ] Lock imported page layouts.
  - [ ] Add editor styles/previews sufficient for safe client editing.
  - [ ] Update docs with what the client can safely edit.
- Done when:
  - No unsupported block warnings appear in the editor.
  - Exact pages cannot be accidentally rearranged without intentionally unlocking.
  - Client-editable fields are documented.
  - GPT-5.5 medium subagent review gate passes.

### Phase 9 — Final Visual Parity Pass and Cleanup

- Branch: `feature/wordpress-port`
- Commits:
  - [ ] Run full visual diff suite.
  - [ ] Fix remaining spacing/color/font/image/interaction diffs.
  - [ ] Remove or deprecate unused approximate CSS/patterns if they confuse editor use.
  - [ ] Update `wordpress/README.md` with visual test workflow and deployment checklist.
- Done when:
  - All target pages/viewports pass visual threshold.
  - PHP syntax checks pass.
  - Importer dry-run and actual import pass on a clean local database.
  - Contact endpoint smoke test passes.
  - Final GPT-5.5 medium subagent review gate passes.

## 9. Testing Strategy

- Unit/static checks:
  - PHP syntax check for all changed PHP files.
  - JavaScript syntax check for editor/frontend scripts.
  - JSON validation for all `block.json` files and `theme.json`.

- Integration checks:
  - Start local WordPress with Docker Compose.
  - Run `wp sennen import --dry-run --allow-root`.
  - Run actual import on local DB with `--force-layout` when appropriate.
  - Verify all pages return HTTP 200.
  - Verify all Sennen blocks are registered server-side.
  - Verify the editor script enqueues and no current page contains unsupported blocks.
  - Submit contact form with valid and invalid data against the REST endpoint.

- Visual regression checks:
  - Capture Next.js baselines.
  - Capture WordPress candidates.
  - Compare these route/viewport combinations:
    - `/` at 375, 768, 1440, 1920 widths
    - `/about` at 375, 768, 1440, 1920 widths
    - `/services` at 375, 768, 1440, 1920 widths
    - `/testimonials` at 375, 768, 1440, 1920 widths
    - `/booking` at 375, 768, 1440, 1920 widths
  - Mask dynamic/third-party regions only when necessary, especially Calendly iframe internals.

- Manual checks:
  - Nav scroll state.
  - Mobile menu open/close.
  - Hover states on cards/buttons/images.
  - Keyboard focus visibility.
  - WordPress editor opens each page without unsupported-block warnings.
  - Client-editable fields are discoverable.

- How each phase will be validated before merging:
  - Automated checks for the files touched in the phase.
  - Relevant page visual diffs.
  - Manual smoke check for the affected page/editor behavior.
  - GPT-5.5 medium subagent code review gate.

## 10. Security Implications

- User-controlled content:
  - Page copy, service content, testimonial content, settings, URLs, and contact form submissions are user-controlled.
  - All frontend output must use the correct escaping function (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post` only where HTML is intentionally allowed).

- Contact form:
  - Must retain nonce validation or equivalent CSRF protection.
  - Must retain rate limiting.
  - Must validate email, name, inquiry type, and message length.
  - Must avoid exposing mail errors or sensitive server configuration.

- URLs:
  - Booking URLs must be sanitized with `esc_url_raw` on save and `esc_url` on output.
  - Calendly/embed URL handling must avoid arbitrary script injection.

- Editor/admin:
  - Settings pages must require appropriate capabilities, e.g. `manage_options`.
  - Import command runs through WP-CLI and should not expose web-triggered file/path controls.

- Visual test artifacts:
  - Screenshots should not include secrets or authenticated admin screens.
  - Generated diff images should be treated as local build artifacts unless intentionally committed as baselines.

## 11. Risks & Tradeoffs

- Risk: Pixel-perfect parity conflicts with freeform WordPress editing.
  - Mitigation: Use locked section blocks. Content remains editable; layout changes become developer changes.

- Risk: Tailwind class scanning misses classes built dynamically in PHP strings.
  - Mitigation: Keep class names literal in render files where possible and add an explicit safelist/source file for any dynamic class names.

- Risk: Fonts render slightly differently from Next.js `next/font`.
  - Mitigation: Use the same font families/weights/files where practical and validate in screenshots. Accept tiny anti-aliasing differences only when layout is otherwise aligned.

- Risk: Next.js `Image` output differs from plain WordPress images.
  - Mitigation: Recreate the rendered layout behavior with equivalent wrappers, absolute positioning, object-fit classes, image dimensions, and priority-equivalent loading where relevant.

- Risk: WordPress core/frontend block CSS introduces margins/wrappers.
  - Mitigation: Avoid core layout blocks for exact sections, minimize template wrappers, and add targeted reset rules.

- Risk: Third-party Calendly iframe cannot be pixel-controlled.
  - Mitigation: Match the container and placeholder exactly; mask iframe internals in visual regression.

- Risk: More custom blocks means more code than a generic Gutenberg build.
  - Mitigation: This is the cost of exact parity. Keep blocks section-focused and use shared render helpers to prevent duplication.

- Risk: Visual tests may be brittle.
  - Mitigation: Use same browser engine, fixed viewport sizes, deterministic local content, masked dynamic regions, and a small anti-aliasing threshold.

## 12. Open Questions

No blocking open questions remain for implementation.

Resolved decisions:

- The Next.js frontend is the source of truth.
- Public frontend pixel parity is more important than freeform page layout editing.
- WordPress will use locked custom section blocks for exact pages.
- Services/testimonials/settings remain editable in WordPress.
- Visual parity will be measured against local Next.js screenshots.
- The accepted screenshot threshold is less than `0.5%` changed pixels per route/viewport, with Calendly iframe internals masked when necessary.
- A GPT-5.5 medium subagent code review gate is required after every implementation phase.
