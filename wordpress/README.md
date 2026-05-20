# WordPress Local Development

Sennen Life Coaching — WordPress block theme and custom plugin.

## Prerequisites

- Docker and Docker Compose
- Git

## Quick Start

```bash
cd wordpress
docker compose up -d
```

Visit:

- **Site:** http://localhost:8080
- **Admin:** http://localhost:8080/wp-admin (`admin` / `admin` initially)
- **MailHog:** http://localhost:8025 (catches outgoing email)

## Theme + Plugin

- **Theme:** `wp-content/themes/sennen` — custom block theme.
- **Plugin:** `wp-content/plugins/sennen-core` — custom blocks, CPTs, contact form.

Both are mounted as volumes in Docker, so edits are reflected immediately. Rebuild containers only if you change `docker-compose.yml`:

```bash
docker compose up -d --build
```

## Import Existing Content

After setup, import the current Sanity content from the repo's `content/` directory:

```bash
docker compose exec wordpress wp sennen import
```

Dry-run first to preview:

```bash
docker compose exec wordpress wp sennen import --dry-run
```

## Stop / Reset

```bash
docker compose down
```

To delete the database and start fresh:

```bash
docker compose down -v
docker compose up -d
```

## Email Testing

MailHog is included. All `wp_mail()` calls are captured at http://localhost:8025 — no real email is sent in local dev.

## WordPress Debug

Debugging is enabled but hidden from the frontend. Check logs:

```bash
docker compose exec wordpress cat wp-content/debug.log
```

Or tail:

```bash
docker compose exec wordpress tail -f wp-content/debug.log
```

## Editor Handoff

For non-technical content editors:

1. Log into WordPress admin at `/wp-admin`.
2. Use **Pages** to edit Home, About, Services, Testimonials, and Booking pages.
3. Use **Services** in the sidebar to manage service cards.
4. Use **Testimonials** in the sidebar to manage testimonial cards.
5. Use **Appearance → Editor** to edit the header, footer, and site-wide layout.
6. Use **Settings → Sennen** to update the booking URL and contact email.

All page sections are built with blocks and patterns. You can drag, reorder, duplicate, and remove sections from the block editor.
