# Sennen Life Coaching Website

A beautiful, CMS-powered life coaching website built with Next.js 16, Tailwind CSS v4, and Sanity CMS.

**Live site:** https://sennenlifecoaching.com

---

## Quick Start

### Prerequisites

- Node.js 18+
- npm or yarn
- Sanity account (free at [sanity.io](https://sanity.io))
- Resend account for contact form emails (free at [resend.com](https://resend.com))

### 1. Clone & Install

```bash
git clone <repository-url>
cd sennen-life-coaching
npm install
```

### 2. Environment Variables

Create a `.env.local` file with your credentials:

```env
# Sanity CMS
NEXT_PUBLIC_SANITY_PROJECT_ID=your-project-id
NEXT_PUBLIC_SANITY_DATASET=production
NEXT_PUBLIC_SANITY_API_VERSION=2025-05-08

# Resend (contact form emails)
RESEND_API_KEY=re_your-api-key
CONTACT_EMAIL=hello@yourdomain.com
```

### 3. Seed Sanity Content

1. Go to [sanity.io/manage](https://sanity.io/manage) → your project → Vision tab
2. Run the mutations from `scripts/seed-sanity-manual.ts` to populate initial content

Or use the CLI:
```bash
npx sanity exec scripts/seed-sanity.ts --with-terminal
```

### 4. Run Locally

```bash
npm run dev
```

Open [http://localhost:3000](http://localhost:3000)

---

## Project Structure

```
sennen-life-coaching/
├── app/
│   ├── api/contact/         # Contact form API endpoint
│   ├── about/               # About page
│   ├── booking/             # Booking page with Calendly embed
│   ├── services/            # Services/pricing page
│   ├── studio/[[...tool]]/   # Sanity Studio
│   ├── layout.tsx           # Root layout with NavBar/Footer
│   ├── page.tsx             # Home page
│   ├── sitemap.ts           # Sitemap
│   └── robots.ts            # Robots.txt
├── components/
│   ├── NavBar.tsx           # Navigation with scroll effects
│   ├── Footer.tsx           # Footer with social links
│   ├── Testimonials.tsx     # Testimonial cards grid
│   ├── BookingEmbed.tsx     # Calendly inline widget
│   └── ContactForm.tsx      # Working contact form
├── sanity/
│   ├── client.ts            # Sanity client
│   ├── fetch.ts             # Data fetching functions
│   ├── queries.ts           # GROQ queries
│   ├── image.ts             # Image URL builder
│   ├── types.ts             # TypeScript interfaces
│   └── schemas/             # CMS content schemas
├── scripts/
│   └── seed-sanity.ts       # Content seeding script
└── public/images/          # Static images
```

---

## Managing Content

Access Sanity Studio at `/studio` to edit:

| Document | What you can edit |
|---|---|
| **Site Settings** | Brand name, tagline, logo, booking URL, contact email, social links |
| **Home Page** | Hero heading, subtitle, CTA text, images, philosophy section |
| **About Page** | Hero content, journey story sections, philosophy cards |
| **Services** | Add/edit/remove service packages with pricing |
| **Testimonials** | Add/edit client testimonials with quotes |

---

## Customizing the Booking

1. Get a free Calendly account at [calendly.com](https://calendly.com)
2. Set up your availability and meeting types
3. Copy your Calendly link (e.g., `https://calendly.com/your-name`)
4. Paste it into **Site Settings → Calendly Booking URL** in Sanity Studio

The Calendly widget will automatically appear on the booking page.

---

## Deployment

### Vercel (Recommended)

1. Push to GitHub
2. Go to [vercel.com](https://vercel.com) → Import Project
3. Select your repo
4. Add environment variables in Vercel dashboard:
   - `NEXT_PUBLIC_SANITY_PROJECT_ID`
   - `NEXT_PUBLIC_SANITY_DATASET`
   - `NEXT_PUBLIC_SANITY_API_VERSION`
   - `RESEND_API_KEY`
   - `CONTACT_EMAIL`
5. Deploy!

### Configure Sanity CORS

After deploying, add your production URL to Sanity CORS origins:
```bash
npx sanity cors add https://your-site.vercel.app
```

---

## Tech Stack

- **Next.js 16** — React framework with App Router
- **Tailwind CSS v4** — Utility-first CSS
- **Sanity CMS v5** — Headless content management
- **Resend** — Email API for contact form
- **Calendly** — Scheduling/booking widget

---

## TODO

- [ ] Replace placeholder images with real photos
- [ ] Update Calendly URL in Site Settings
- [ ] Set up Resend with custom domain for professional email
- [ ] Add actual social media links in Site Settings
- [ ] Create OG image (1200x630) in `public/images/`
- [ ] Point domain to Vercel deployment