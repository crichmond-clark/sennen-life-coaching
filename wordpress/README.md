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

After setup, import the current Sanity content from the repo's `content/` directory. The importer upserts pages/posts, sets Home as the front page, and uses custom Sennen blocks for dynamic sections:

```bash
docker compose exec wordpress wp sennen import --allow-root
```

Dry-run first to preview:

```bash
docker compose exec wordpress wp sennen import --dry-run --allow-root
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
2. Use **Services** in the sidebar to manage service packages (title, price, duration, features, description).
3. Use **Testimonials** in the sidebar to manage testimonial cards (author name, author title, quote).
4. Use **Settings → Sennen** to update the booking URL and contact email.
5. Page copy (headings, subtitles, body text) can be edited through each page's block attributes in the editor.

**Important:** Page layouts are locked. The exact section blocks (hero, philosophy, cards, etc.) maintain visual parity with the original Next.js frontend. Do NOT unlock or rearrange blocks unless you accept that parity may break.

What the client can safely edit:
- Service packages (title, price, duration, features, description)
- Testimonials (author, title, quote)
- Page headings and body text through block attributes
- Booking URL and contact email through Settings
- Site name and tagline through Settings → General

What should NOT be changed without developer support:
- Block order on pages
- Adding/removing section blocks
- Theme CSS or template files
- Plugin code

## Production Hardening

Recommended configuration in `wp-config.php`:

```php
define('DISALLOW_FILE_EDIT', true);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', true);
```

Security best practices:

- Keep WordPress core, the `sennen` theme, and `sennen-core` plugin updated.
- Remove unused default themes (Twenty\u2010*).
- Remove inactive plugins.
- Use strong passwords for all admin/editor accounts.
- Enable two-factor authentication at the host level if available.
- Set file permissions: directories 755, files 644, `wp-config.php` 600.
- Use SFTP/SSH, not plain FTP.
- Configure host-level backups (daily database + files).
- Lock down XML-RPC if not needed.
- Do not use the `admin` username for production.

Performance recommendations:

- Enable PHP OPcache at the host level.
- Enable host-level page caching if available.
- Use WebP for uploaded images (WordPress 6.7+ supports this natively).
- Consider a free caching plugin only if needed; the custom theme is already lean.
- Monitor for slow queries in Query Monitor during development.
