# Sennen Life Coaching Website

Static Astro website for Sennen Life Coaching.

**Live site:** https://sennenlifecoaching.com

## Launch TODO

See [`docs/post-migration-todo.md`](docs/post-migration-todo.md) for the remaining review, content, booking/contact, SEO, and deployment tasks before launch.

## Stack

- **Astro** — static site generator
- **Tailwind CSS v4** — styling and design tokens
- **Markdown** — page copy
- **YAML** — settings, services, testimonials, FAQs
- **Calendly** — booking link/embed
- **Static form provider or mailto** — contact form handling

## Local Development

```bash
npm install
npm run dev
```

Open http://localhost:4321.

## Content Editing

Public copy is kept out of components and lives in `src/content/`.

```txt
src/content/
  pages/          # Markdown page copy and SEO metadata
  services/       # YAML service/package records
  testimonials/   # YAML testimonial records
  settings/       # YAML site settings
  faqs/           # YAML FAQ entries
```

Edit these files, then run:

```bash
npm run check
npm run build
```

## Site Settings

Edit `src/content/settings/site.yaml` for:

- brand name
- tagline
- site URL
- contact email
- Calendly booking URL
- contact form provider endpoint
- social links

Empty social link URLs are hidden in the footer.

## Contact Form

The site is fully static, so it does not include a backend email API.

Options:

1. Add a Formspree/Basin/Getform endpoint to `formAction` in `src/content/settings/site.yaml`.
2. Leave `formAction` empty to use the current `mailto:` fallback.
3. If hosting on Netlify, adapt `src/components/ContactForm.astro` for Netlify Forms.

## Booking

Add a Calendly URL to `bookingUrl` in `src/content/settings/site.yaml`.

If `bookingUrl` is empty, the booking panel shows a fallback message and links to the contact form.

## Commands

```bash
npm run dev      # Start Astro dev server
npm run check    # Validate Astro and content collections
npm run build    # Build static site into dist/
npm run preview  # Preview production build locally
```

## Deployment

The site builds to static files in `dist/` and can be hosted on Vercel, Netlify, Cloudflare Pages, or any static host.

Recommended build command:

```bash
npm run build
```

Output directory:

```txt
dist
```
