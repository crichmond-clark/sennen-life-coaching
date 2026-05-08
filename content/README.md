# Editing Content Without Sanity Studio

All site content lives as JSON files in this folder. Edit them in any text editor, then push to Sanity with one command.

## Setup (one-time)

You need a Sanity API token with **Editor** permissions.

1. Go to: `https://www.sanity.io/manage/personal/project/f0ruwhgq/api#tokens`
2. Click **Add API token**
3. Name it `content-push`, set role to **Editor**
4. Copy the token
5. Add it to your `.env.local`:
   ```
   SANITY_API_TOKEN=your-token-here
   ```

## Editing workflow

1. **Edit the JSON files** in this folder:
   - `site-settings.json` — brand name, tagline, booking URL, contact email, social links
   - `home.json` — hero text, philosophy section
   - `about.json` — hero text, journey sections, philosophy cards
   - `services.json` — service packages (add/remove/edit)
   - `testimonials.json` — client testimonials (add/remove/edit)

2. **Push to Sanity:**
   ```bash
   npm run content:push
   ```

3. **Wait ~60 seconds** for the site to refresh (ISR), then hard-refresh the page.

## Important notes

- **Images**: You can't upload images via JSON. For now, either use the Studio at `/studio` to upload images, or use external image URLs (not supported yet — would need code changes).
- **Don't change `_type` or `_id`** — those tie the JSON to Sanity's schema.
- **Services and testimonials** are arrays — add new objects to the list, or remove ones you don't want.
- **Social links**: Leave the `url` as `""` to hide that platform from the footer.
- **Portable text** (the `philosophyBody` array in `home.json`): Each block is a paragraph. Edit the `text` field inside `children`.
