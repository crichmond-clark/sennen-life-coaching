# Sennen Life Coaching Website — Implementation Plan

## 1. Problem Statement

Sennen needs a beautiful, professional life coaching website that she can maintain herself without developer assistance. The design direction is established ("Bali Soul" design system) and a Next.js + Tailwind boilerplate has been generated via Stitch/AI Studio. We need to turn that boilerplate into a production-ready site with a CMS, real images, booking integration, and proper branding.

## 2. Goals & Non-Goals

**Goals:**
- Production-ready website deployed on Vercel (free tier)
- Sennen can edit all page content, services, and testimonials via Sanity Studio
- Real booking integration (Calendly embed)
- Working contact form that sends emails via Resend
- Proper images (Unsplash placeholders until Sennen provides real photos)
- Branded as "Sennen Life Coaching"
- All major social platforms in footer (Instagram, TikTok, LinkedIn, Facebook, X/Twitter)
- SEO basics (meta tags, Open Graph, sitemap)
- Mobile responsive and accessible

**Non-Goals:**
- Blog/journal section (can add later)
- User accounts or authentication
- Payment processing through the site (handled by Calendly)
- Multi-language support
- E-commerce / merchandise store

## 3. Proposed Architecture

**Stack:**
- **Next.js 15** (App Router) — already in the boilerplate
- **Tailwind CSS v4** — already in the boilerplate
- **Sanity CMS v3** — headless CMS for content management
  - Sanity Studio embedded at `/studio` route within the Next.js app
  - Content delivered via Sanity's API using GROQ queries
  - Free tier is sufficient (100K API CDN requests, 1GB assets)
- **Vercel** — hosting (free tier, static/ISR)
- **Calendly** — booking widget (inline embed, free tier with 1 event type)
- **Resend** — contact form submissions → email to Sennen (free tier, 100 emails/day)
- **next-sanity** — official Sanity/Next.js integration for fetch, image URL builder, Studio routing

**Key decisions:**
- **Sanity over Payload:** No server to manage. Built-in image CDN with on-the-fly transforms. Free tier covers this use case. Studio is polished for non-technical editors.
- **Resend over Formspree:** More flexible, proper Node.js SDK, generous free tier.
- **ISR (Incremental Static Regeneration):** Pages built at request time with revalidation (60s). Fast responses, fresh content without full rebuilds.

**Project structure:**
```
sennen-life-coaching/
├── app/
│   ├── globals.css
│   ├── layout.tsx            # Root layout (fonts, nav, footer, site settings fetch)
│   ├── page.tsx              # Home (hero + philosophy + testimonials + CTA)
│   ├── about/page.tsx        # About (hero + journey + philosophy cards + gallery)
│   ├── services/page.tsx     # Services (intro + philosophy + package cards + bespoke CTA)
│   ├── booking/page.tsx      # Booking (Calendly embed + contact form + FAQ)
│   ├── studio/[[...tool]]/
│   │   └── page.tsx          # Sanity Studio (Next.js route handler)
│   └── api/
│       └── contact/
│           └── route.ts      # POST handler → Resend email
├── components/
│   ├── NavBar.tsx
│   ├── Footer.tsx
│   ├── Testimonials.tsx      # Testimonial grid on home page
│   ├── BookingEmbed.tsx      # Calendly inline widget wrapper
│   └── ContactForm.tsx       # Client component with validation + submission
├── sanity/
│   ├── client.ts             # Sanity client (read-only, public dataset)
│   ├── schema.ts             # Schema index / exports
│   ├── env.ts                # Env var exports (projectId, dataset)
│   ├── image.ts              # Image URL builder helper
│   ├── schemas/
│   │   ├── siteSettings.ts   # Singleton: brand name, tagline, logo, socials, booking URL
│   │   ├── home.ts           # Singleton: hero content, philosophy section
│   │   ├── about.ts          # Singleton: hero, journey sections, philosophy cards
│   │   ├── service.ts        # Document: title, slug, price, duration, description, features
│   │   └── testimonial.ts    # Document: quote, author name, author title, avatar
│   └── types.ts              # TypeScript types generated from schemas
├── lib/
│   └── utils.ts              # cn() helper (existing)
├── hooks/
│   └── use-mobile.ts         # (existing)
├── public/
│   ├── images/
│   │   └── logo.jpg          # Sennen's logo
│   ├── favicon.ico
│   └── robots.txt
├── sanity.config.ts          # Root Sanity config
├── docs/
│   └── sennen-life-coaching-plan.md
├── next.config.ts
├── tailwind.config.ts        # (if needed, otherwise globals.css @theme)
├── package.json
├── tsconfig.json
├── .env.example
├── .env.local                # (gitignored)
└── .gitignore
```

## 4. Component Breakdown

### CMS Schemas

**siteSettings (singleton):**
- `brandName` (string) — "Sennen Life Coaching"
- `tagline` (string) — "Rooted in Grace"
- `logo` (image)
- `bookingUrl` (url) — Calendly scheduling link
- `socialLinks` (array of objects: platform + url)
  - Instagram, TikTok, LinkedIn, Facebook, X/Twitter
- `contactEmail` (string) — where contact form submissions go

**home (singleton):**
- `heroHeading` (string)
- `heroSubtitle` (string)
- `heroCtaText` (string)
- `heroImage` (image)
- `philosophyHeading` (string)
- `philosophyBody` (array of text blocks / portable text)
- `philosophyImage` (image)

**about (singleton):**
- `heroHeading` (string)
- `heroSubtitle` (string)
- `heroImage` (image)
- `journeySections` (array of objects: heading, body, image)
- `philosophyCards` (array of objects: title, body, iconName)

**service (document, multiple):**
- `title` (string)
- `slug` (slug from title)
- `price` (string)
- `duration` (string)
- `description` (text)
- `features` (array of strings)
- `sortOrder` (number)

**testimonial (document, multiple):**
- `quote` (text)
- `authorName` (string)
- `authorTitle` (string)
- `avatar` (image, optional)

### Frontend Components

- `NavBar` — fetches brand name + logo from siteSettings. Transparent → frosted glass on scroll. Mobile hamburger with motion animations. CTA button links to /booking.
- `Footer` — fetches brand name, tagline, social links from siteSettings. Social icons via Lucide.
- `Testimonials` — fetches testimonials from Sanity. Grid of quote cards on home page.
- `BookingEmbed` — Calendly inline widget. URL from siteSettings. Loads Calendly script.
- `ContactForm` — Client component. Fields: name, email, inquiry type (select), message. Client-side validation. Submits to `/api/contact`. Shows success/error states.

## 5. Data Flow

1. Sennen logs into Sanity Studio at `/studio` (authenticated via Sanity — Google/GitHub SSO)
2. She edits content (text, images, services, testimonials)
3. Next.js pages fetch from Sanity using GROQ queries with ISR (revalidate: 60)
4. Images served via `cdn.sanity.io` with automatic optimization and cropping
5. Home page: fetches `home` singleton + `testimonial` documents + `siteSettings`
6. About page: fetches `about` singleton + `siteSettings`
7. Services page: fetches all `service` documents (ordered by sortOrder) + `siteSettings`
8. Booking page: fetches `siteSettings` (for bookingUrl) + renders Calendly embed + contact form
9. Contact form: POST to `/api/contact` → Resend SDK → email to Sennen
10. All pages share `layout.tsx` which fetches `siteSettings` for NavBar/Footer

## 6. Interface Contracts

### Sanity GROQ Queries

**siteSettings (used in layout.tsx):**
```groq
*[_type == "siteSettings"][0]{
  brandName, tagline, bookingUrl, contactEmail,
  logo{asset->{url, metadata}},
  socialLinks[]{platform, url}
}
```

**home page:**
```groq
{
  "home": *[_type == "home"][0]{
    heroHeading, heroSubtitle, heroCtaText,
    heroImage{asset->{url, metadata}},
    philosophyHeading, philosophyBody,
    philosophyImage{asset->{url, metadata}}
  },
  "testimonials": *[_type == "testimonial"]{
    quote, authorName, authorTitle,
    avatar{asset->{url, metadata}}
  }
}
```

**about page:**
```groq
*[_type == "about"][0]{
  heroHeading, heroSubtitle,
  heroImage{asset->{url, metadata}},
  journeySections[]{heading, body, image{asset->{url, metadata}}},
  philosophyCards[]{title, body, iconName}
}
```

**services page:**
```groq
*[_type == "service"] | order(sortOrder asc){
  title, slug, price, duration, description, features
}
```

### API Endpoints

**POST /api/contact**
```typescript
// Input
{
  name: string       // required, min 2 chars
  email: string      // required, valid email format
  inquiryType: string // one of: "General Question", "Bespoke Retreat", "Virtual Session", "In-Person Session"
  message: string    // required, min 10 chars
}

// Success response
{ success: true }

// Error responses
{ error: "Missing required fields" }          // 400
{ error: "Invalid email address" }            // 400
{ error: "Failed to send message. Please try again." } // 500
```

Implementation: Validate input server-side → call Resend SDK `resend.emails.send()` with Sennen's email as recipient → return success/error.

## 7. File Changes

### Create:
- `sanity/env.ts` — Export `projectId` and `dataset` from `process.env`
- `sanity/client.ts` — Sanity read client using `next-sanity`'s `createClient()`
- `sanity/image.ts` — Image URL builder using `@sanity/image-url`
- `sanity/schema.ts` — Import and export all schemas
- `sanity/types.ts` — TypeScript interfaces for all schema types
- `sanity/schemas/siteSettings.ts` — Site settings singleton schema
- `sanity/schemas/home.ts` — Home page singleton schema
- `sanity/schemas/about.ts` — About page singleton schema
- `sanity/schemas/service.ts` — Service document schema
- `sanity/schemas/testimonial.ts` — Testimonial document schema
- `sanity.config.ts` — Root Sanity config with plugins (structureTool)
- `app/studio/[[...tool]]/page.tsx` — Sanity Studio Next.js route
- `app/api/contact/route.ts` — POST handler: validate → Resend email
- `components/Testimonials.tsx` — Testimonial cards grid
- `components/BookingEmbed.tsx` — Calendly inline widget wrapper
- `components/ContactForm.tsx` — Client-side form with validation
- `public/images/logo.jpg` — Sennen's logo (copied from unnamed.jpg)
- `public/robots.txt` — Basic robots.txt
- `docs/sennen-life-coaching-plan.md` — This plan

### Modify:
- `app/layout.tsx` — Fetch `siteSettings` from Sanity for brand name, logo, nav, footer
- `app/page.tsx` — Fetch `home` + `testimonials` from Sanity, add Testimonials section
- `app/about/page.tsx` — Fetch `about` content from Sanity
- `app/services/page.tsx` — Fetch `service` documents from Sanity
- `app/booking/page.tsx` — Replace fake calendar with Calendly embed component, use ContactForm component
- `components/NavBar.tsx` — Accept `brandName` and `logo` as props (or fetch), "Sennen Life Coaching" default
- `components/Footer.tsx` — Accept `brandName`, `tagline`, `socialLinks` as props, add social icons
- `app/globals.css` — Keep design system tokens, remove any unused Google URL references
- `package.json` — Remove `@google/genai`, `firebase-tools`. Add: `next-sanity`, `@sanity/image-url`, `@sanity/vision`, `sanity`, `resend`, `next-sitemap`
- `next.config.ts` — Remove Google/picsum remotePatterns. Add `cdn.sanity.io`. Remove AI Studio HMR hack. Remove `@google/genai` related config.
- `.env.example` — Replace Gemini vars with: `NEXT_PUBLIC_SANITY_PROJECT_ID`, `NEXT_PUBLIC_SANITY_DATASET`, `RESEND_API_KEY`
- `.gitignore` — Add `.env.local`

### Delete:
- `stitch-export/` — Everything moved to root
- `zip.zip` — No longer needed
- `metadata.json` — Stitch metadata
- `app/template.tsx` — Motion page transitions (optional, causes layout shift issues with ISR)

## 8. Implementation Phases

### Phase 1 — Project Setup & Cleanup
- Branch: `main`
- Commits:

  **1a. Move boilerplate to root and clean up**
  ```
  - Move all files from stitch-export/ to project root
  - Delete stitch-export/, zip.zip, metadata.json
  - Copy unnamed.jpg → public/images/logo.jpg
  ```
  Files touched: entire project root restructure

  **1b. Clean package.json**
  ```
  - name: "sennen-life-coaching"
  - description: "Sennen Life Coaching — Rooted in Grace"
  - Remove: @google/genai, firebase-tools dependencies
  - Remove: clean script
  - Add: sanity, next-sanity, @sanity/image-url, @sanity/vision (save for phase 2)
  ```
  File: `package.json`

  **1c. Clean next.config.ts**
  ```
  - Remove picsum.photos and lh3.googleusercontent.com remotePatterns
  - Add cdn.sanity.io remotePattern
  - Remove DISABLE_HMR webpack hack
  - Remove output: 'standalone' (not needed for Vercel)
  - Remove transpilePackages: ['motion'] (works without it)
  ```
  File: `next.config.ts`

  **1d. Rebrand to Sennen Life Coaching**
  ```
  - layout.tsx: Update metadata title to "Sennen Life Coaching" and description
  - NavBar.tsx: Change "Bali Soul" → "Sennen Life Coaching"
  - Footer.tsx: Change "Bali Soul" → "Sennen Life Coaching", update footer links
  - page.tsx (home): Update any "Bali Soul" references in hardcoded text
  - about/page.tsx: Update any "Bali Soul" references
  - services/page.tsx: Update any "Bali Soul" references
  - booking/page.tsx: Update any "Bali Soul" references
  ```
  Files: `app/layout.tsx`, `components/NavBar.tsx`, `components/Footer.tsx`, all page files

  **1e. Replace external Google images with local placeholders**
  ```
  - Download 8-10 Unsplash images matching the Bali Soul aesthetic (nature, meditation, wellness, tropical)
  - Save to public/images/ with descriptive names (hero.jpg, philosophy.jpg, meditation.jpg, etc.)
  - Update all Image src props across all pages to use local paths
  ```
  Files: `app/page.tsx`, `app/about/page.tsx`, `app/services/page.tsx`, `app/booking/page.tsx`, `public/images/*`

  **1f. Update environment configuration**
  ```
  - Update .env.example with Sanity + Resend vars
  - Update .gitignore to include .env.local
  - Remove .env.example Gemini reference
  ```
  Files: `.env.example`, `.gitignore`

  **1g. Remove template.tsx (motion page transitions)**
  ```
  - Delete app/template.tsx — causes layout shift with ISR, not worth the complexity
  ```
  File: `app/template.tsx` (deleted)

  **1h. Update footer with social link placeholders**
  ```
  - Add Instagram, TikTok, LinkedIn, Facebook, X/Twitter links to Footer component
  - Use Lucide icons for each platform
  - Links as # placeholders for now (will be Sanity-driven later)
  ```
  File: `components/Footer.tsx`

- Done when: `npm install && npm run dev` starts clean, all 4 pages render with "Sennen Life Coaching" branding, local images, no console errors

---

### Phase 2 — Sanity CMS Setup
- Branch: `feature/sanity-cms`
- Commits:

  **2a. Install Sanity dependencies**
  ```
  npm install next-sanity @sanity/image-url @sanity/vision sanity
  ```
  File: `package.json` (updated)

  **2b. Create Sanity environment config**
  ```typescript
  // sanity/env.ts
  export const apiVersion = process.env.NEXT_PUBLIC_SANITY_API_VERSION || '2025-05-08'
  export const dataset = assertValue(process.env.NEXT_PUBLIC_SANITY_DATASET, 'Missing dataset')
  export const projectId = assertValue(process.env.NEXT_PUBLIC_SANITY_PROJECT_ID, 'Missing project ID')

  function assertValue<T>(v: T | undefined, errorMessage: string): T {
    if (v === undefined) throw new Error(errorMessage)
    return v
  }
  ```
  File: `sanity/env.ts`

  **2c. Create Sanity client**
  ```typescript
  // sanity/client.ts
  import { createClient } from 'next-sanity'
  import { apiVersion, dataset, projectId } from './env'

  export const client = createClient({
    projectId,
    dataset,
    apiVersion,
    useCdn: true, // CDN for fast responses
  })
  ```
  File: `sanity/client.ts`

  **2d. Create image URL builder**
  ```typescript
  // sanity/image.ts
  import createImageUrlBuilder from '@sanity/image-url'
  import { client } from './client'

  export function urlFor(source: any) {
    return createImageUrlBuilder(client).image(source)
  }
  ```
  File: `sanity/image.ts`

  **2e. Create all Sanity schemas**
  ```
  siteSettings.ts:
    - brandName (string, required)
    - tagline (string)
    - logo (image, options: {hotspot: true})
    - bookingUrl (url)
    - contactEmail (email)
    - socialLinks (array of objects):
      - platform (string, options list: Instagram, TikTok, LinkedIn, Facebook, X)
      - url (url)

  home.ts:
    - heroHeading (string)
    - heroSubtitle (string)
    - heroCtaText (string, initialValue: "Begin Your Journey")
    - heroImage (image, hotspot)
    - philosophyHeading (string)
    - philosophyBody (array of block content / portable text)
    - philosophyImage (image, hotspot)

  about.ts:
    - heroHeading (string)
    - heroSubtitle (string)
    - heroImage (image, hotspot)
    - journeySections (array):
      - heading (string)
      - body (text)
      - image (image, hotspot)
    - philosophyCards (array):
      - title (string)
      - body (text)
      - iconName (string, options list: Flower, Droplet, Leaf, Sun, Heart)

  service.ts:
    - title (string)
    - slug (slug from title)
    - price (string)
    - duration (string)
    - description (text)
    - features (array of strings)
    - sortOrder (number)

  testimonial.ts:
    - quote (text)
    - authorName (string)
    - authorTitle (string)
    - avatar (image, hotspot, optional)
  ```
  Files: `sanity/schemas/siteSettings.ts`, `sanity/schemas/home.ts`, `sanity/schemas/about.ts`, `sanity/schemas/service.ts`, `sanity/schemas/testimonial.ts`

  **2f. Create schema index**
  ```typescript
  // sanity/schema.ts
  import { siteSettings } from './schemas/siteSettings'
  import { home } from './schemas/home'
  import { about } from './schemas/about'
  import { service } from './schemas/service'
  import { testimonial } from './schemas/testimonial'

  export const schemaTypes = [siteSettings, home, about, service, testimonial]
  ```
  File: `sanity/schema.ts`

  **2g. Create TypeScript types**
  ```typescript
  // sanity/types.ts — interfaces matching each schema
  export interface SiteSettings { ... }
  export interface Home { ... }
  export interface About { ... }
  export interface Service { ... }
  export interface Testimonial { ... }
  ```
  File: `sanity/types.ts`

  **2h. Create sanity.config.ts**
  ```typescript
  import { defineConfig } from 'sanity'
  import { structureTool } from 'sanity/structure'
  import { schemaTypes } from './sanity/schema'

  export default defineConfig({
    name: 'sennen-life-coaching',
    title: 'Sennen Life Coaching',
    projectId: process.env.NEXT_PUBLIC_SANITY_PROJECT_ID!,
    dataset: process.env.NEXT_PUBLIC_SANITY_DATASET!,
    plugins: [structureTool()],
    schema: { types: schemaTypes },
  })
  ```
  File: `sanity.config.ts`

  **2i. Create Sanity Studio route**
  ```typescript
  // app/studio/[[...tool]]/page.tsx
  'use client'
  import { NextStudio } from 'next-sanity/studio'
  import config from '../../../../sanity.config'

  export default function StudioPage() {
    return <NextStudio config={config} />
  }

  // Also need layout.tsx to disable NavBar/Footer for studio route
  ```
  Files: `app/studio/[[...tool]]/page.tsx`, `app/studio/[[...tool]]/layout.tsx`

  **2j. Create initial content seed script**
  ```
  - Create scripts/seed-sanity.ts
  - Populate siteSettings with "Sennen Life Coaching" defaults
  - Populate home with current hardcoded content
  - Populate about with current hardcoded content
  - Create 3 service documents matching current packages
  - Create 3 sample testimonials
  - Run against Sanity API to populate dataset
  ```
  File: `scripts/seed-sanity.ts`

  **2k. Update next.config.ts for Sanity images**
  ```
  images:
    remotePatterns:
      - protocol: 'https'
        hostname: 'cdn.sanity.io'
  ```
  File: `next.config.ts`

- Done when: `npm run dev` works, `/studio` loads Sanity Studio, all document types are visible and editable, seed data populates successfully

---

### Phase 3 — Dynamic Content from Sanity
- Branch: `feature/dynamic-content`
- Commits:

  **3a. Create shared data fetching utilities**
  ```typescript
  // sanity/queries.ts
  // Export all GROQ query strings as constants
  export const siteSettingsQuery = `*[_type == "siteSettings"][0]{ ... }`
  export const homeQuery = `{ "home": *[_type == "home"][0]{ ... }, "testimonials": *[_type == "testimonial"]{ ... } }`
  export const aboutQuery = `*[_type == "about"][0]{ ... }`
  export const servicesQuery = `*[_type == "service"] | order(sortOrder asc){ ... }`

  // sanity/fetch.ts
  import { client } from './client'
  import { siteSettingsQuery, ... } from './queries'

  export async function getSiteSettings() { return client.fetch(siteSettingsQuery) }
  export async function getHome() { return client.fetch(homeQuery) }
  // etc.
  ```
  Files: `sanity/queries.ts`, `sanity/fetch.ts`

  **3b. Update layout.tsx to use Sanity site settings**
  ```
  - Import getSiteSettings
  - Fetch in layout (server component)
  - Pass brandName to NavBar and Footer as props
  - Pass logo to NavBar
  - Pass socialLinks to Footer
  - Update metadata to use brandName from Sanity
  - Add revalidate: 60
  ```
  File: `app/layout.tsx`

  **3c. Update NavBar to use props from Sanity**
  ```
  - Accept brandName and logo as props (or fall back to defaults)
  - Render brandName text (or logo image if provided)
  - Keep existing scroll detection, mobile menu, CTA button
  ```
  File: `components/NavBar.tsx`

  **3d. Update Footer to use props from Sanity**
  ```
  - Accept brandName, tagline, socialLinks as props
  - Render social icons (Lucide: Instagram, Linkedin, Facebook, Twitter)
  - For TikTok use a custom SVG or Music2 icon as fallback
  - Each social link renders as <a href={url} target="_blank">
  ```
  File: `components/Footer.tsx`

  **3e. Update home page to fetch from Sanity**
  ```
  - Import getHome from sanity/fetch
  - Fetch home singleton + testimonials
  - Replace all hardcoded text with Sanity values
  - Replace Image src with Sanity image URLs via urlFor()
  - Add revalidate = 60 for ISR
  - Pass testimonials to Testimonials component (created in Phase 4)
  ```
  File: `app/page.tsx`

  **3f. Update about page to fetch from Sanity**
  ```
  - Import getAbout from sanity/fetch
  - Fetch about singleton
  - Map journeySections to the grid layout
  - Map philosophyCards to the card grid (use iconName to render Lucide icons)
  - Replace all hardcoded text and images
  - Add revalidate = 60
  ```
  File: `app/about/page.tsx`

  **3g. Update services page to fetch from Sanity**
  ```
  - Import getServices from sanity/fetch
  - Fetch all service documents
  - Map to package cards (same layout, dynamic data)
  - Replace hardcoded packages array with Sanity data
  - Add revalidate = 60
  ```
  File: `app/services/page.tsx`

  **3h. Create Sanity image component helper**
  ```typescript
  // components/SanityImage.tsx
  // Wrapper around next/image that handles Sanity image URLs
  // Uses urlFor() to generate proper src with width/height/format params
  // Accepts: value (Sanity image object), width, height, alt, className, fill
  ```
  File: `components/SanityImage.tsx`

- Done when: All 4 pages render content from Sanity. Editing any document in Studio and refreshing the page shows updated content. All images load from Sanity CDN or local fallbacks.

---

### Phase 4 — Testimonials & Booking Integration
- Branch: `feature/testimonials-booking`
- Commits:

  **4a. Create Testimonials component**
  ```typescript
  // components/Testimonials.tsx
  // Props: testimonials: Testimonial[]
  // Layout: Section with heading "What Clients Say" + grid of testimonial cards
  // Each card: quote text, author name, author title, optional avatar
  // Style: surface-container-lowest background, rounded-3xl, ambient-shadow
  // Responsive: 1 col mobile, 3 cols desktop
  ```
  File: `components/Testimonials.tsx`

  **4b. Add testimonials section to home page**
  ```
  - Add Testimonials component after the philosophy section
  - Pass testimonials from Sanity fetch (already fetched in Phase 3)
  - Add a botanical divider before testimonials
  - Add a CTA section after testimonials ("Ready to begin? → Book a Session")
  ```
  File: `app/page.tsx`

  **4c. Create BookingEmbed component**
  ```typescript
  // components/BookingEmbed.tsx
  'use client'
  // Props: bookingUrl: string
  // Renders Calendly inline widget
  // Uses Calendly's embed script: https://assets.calendly.com/assets/external/widget.js
  // Sets data-url={bookingUrl} on a div with class "calendly-inline-widget"
  // Set min-width: 320px, height: 700px
  // Load script in useEffect to avoid SSR issues
  ```
  File: `components/BookingEmbed.tsx`

  **4d. Update booking page**
  ```
  - Remove entire fake calendar section (the days grid, time slots, confirm button)
  - Replace with BookingEmbed component using bookingUrl from siteSettings
  - Keep the contact form column (will be wired in Phase 5)
  - Keep the FAQ section
  - Fetch siteSettings for bookingUrl
  ```
  File: `app/booking/page.tsx`

- Done when: Testimonials render on home page, Calendly widget loads and is interactive on booking page, contact form still displays (not yet functional)

---

### Phase 5 — Contact Form & Email
- Branch: `feature/contact-form`
- Commits:

  **5a. Install Resend**
  ```
  npm install resend
  ```
  File: `package.json`

  **5b. Create ContactForm client component**
  ```typescript
  // components/ContactForm.tsx
  'use client'
  // State: formData, errors, submitStatus ('idle' | 'submitting' | 'success' | 'error')
  // Fields: name, email, inquiryType (select), message
  // Validation: required fields, email regex, message min 10 chars
  // onSubmit: POST to /api/contact with JSON body
  // Success state: green message "Your message has been sent"
  // Error state: red message "Something went wrong, please try again"
  // Disable submit button while submitting
  ```
  File: `components/ContactForm.tsx`

  **5c. Create /api/contact route handler**
  ```typescript
  // app/api/contact/route.ts
  import { Resend } from 'resend'
  import { NextRequest, NextResponse } from 'next/server'

  const resend = new Resend(process.env.RESEND_API_KEY)

  export async function POST(req: NextRequest) {
    // 1. Parse JSON body
    // 2. Validate: name (min 2), email (regex), inquiryType (enum), message (min 10)
    // 3. Send email via Resend:
    //    from: 'onboarding@resend.dev' (or Sennen's verified domain later)
    //    to: siteSettings.contactEmail (fetch from Sanity)
    //    subject: `New inquiry from ${name} — ${inquiryType}`
    //    html: formatted email with all fields
    // 4. Return { success: true } or { error: message }
    // 5. Wrap in try/catch, return 500 on Resend error
  }
  ```
  File: `app/api/contact/route.ts`

  **5d. Update booking page to use ContactForm**
  ```
  - Replace the inline form JSX with <ContactForm />
  - Keep the form heading and description text
  - Remove the inline send button and form fields
  ```
  File: `app/booking/page.tsx`

- Done when: Submitting contact form sends an email to Sennen's address, form shows success/error feedback, form validates inputs client-side and server-side

---

### Phase 6 — SEO, Meta & Deploy
- Branch: `feature/seo-deploy`
- Commits:

  **6a. Add dynamic metadata to all pages**
  ```
  - layout.tsx: Generate metadata from Sanity siteSettings
    - title template: "%s | Sennen Life Coaching"
    - default title: "Sennen Life Coaching — Rooted in Grace"
    - description from siteSettings tagline or hardcoded default
    - Open Graph: title, description, image (logo or hero)
    - Twitter card: summary_large_image

  - page.tsx: export metadata with "Home" title, hero-specific description
  - about/page.tsx: export metadata with "About" title
  - services/page.tsx: export metadata with "Services" title
  - booking/page.tsx: export metadata with "Book a Session" title
  ```
  Files: `app/layout.tsx`, `app/page.tsx`, `app/about/page.tsx`, `app/services/page.tsx`, `app/booking/page.tsx`

  **6b. Create favicon from logo**
  ```
  - Convert logo.jpg to favicon.ico (can use sharp or an online tool)
  - Place in app/favicon.ico (Next.js App Router convention)
  - Also create apple-touch-icon.png (180x180)
  ```
  Files: `app/favicon.ico`, `app/apple-touch-icon.png`

  **6c. Create robots.txt and sitemap**
  ```
  // app/robots.ts — Next.js built-in robots.txt generation
  export default function robots() {
    return {
      rules: { userAgent: '*', allow: '/' },
      sitemap: 'https://sennenlifecoaching.com/sitemap.xml',
    }
  }

  // app/sitemap.ts — Next.js built-in sitemap generation
  export default async function sitemap() {
    return [
      { url: 'https://sennenlifecoaching.com', lastModified: new Date() },
      { url: 'https://sennenlifecoaching.com/about', lastModified: new Date() },
      { url: 'https://sennenlifecoaching.com/services', lastModified: new Date() },
      { url: 'https://sennenlifecoaching.com/booking', lastModified: new Date() },
    ]
  }
  ```
  Files: `app/robots.ts`, `app/sitemap.ts`

  **6d. Final responsive/accessibility pass**
  ```
  - Check all pages on mobile viewport (375px)
  - Ensure all images have alt text
  - Ensure all form inputs have labels
  - Check color contrast ratios (primary on background, etc.)
  - Ensure keyboard navigation works (focus states visible)
  - Check NavBar mobile menu keyboard trap
  ```
  Files: various component files

  **6e. Create Sanity CORS config and deploy**
  ```
  - Run: sanity cors add https://sennen-life-coaching.vercel.app
  - Run: sanity cors add http://localhost:3000
  - Deploy to Vercel: vercel (or connect GitHub repo in Vercel dashboard)
  - Set environment variables in Vercel:
    - NEXT_PUBLIC_SANITY_PROJECT_ID
    - NEXT_PUBLIC_SANITY_DATASET
    - NEXT_PUBLIC_SANITY_API_VERSION
    - RESEND_API_KEY
  - Verify production site loads
  - Verify Sanity Studio loads at production /studio
  ```

  **6f. Write README for handoff**
  ```
  # README.md
  - Project overview
  - Tech stack
  - Local dev setup (npm install, .env.local)
  - How to edit content (Sanity Studio at /studio)
  - How to deploy (push to main → Vercel auto-deploys)
  - How to update booking URL (in Sanity siteSettings)
  - How to update social links (in Sanity siteSettings)
  ```
  File: `README.md`

- Done when: Site is live on Vercel, all pages have proper meta tags, favicon loads, sitemap accessible, Sennen can log into /studio and edit content, Lighthouse score > 90

## 9. Testing Strategy

**Manual testing per phase:**

| Phase | Test | How |
|---|---|---|
| 1 | All pages render | `npm run dev`, click through all routes |
| 1 | No console errors | Check browser dev tools |
| 1 | Brand is "Sennen Life Coaching" | Visual check nav + footer |
| 1 | Images load locally | No broken image icons |
| 2 | Sanity Studio loads | Visit `/studio`, log in |
| 2 | All schemas visible | Check each document type in Studio |
| 2 | Can create documents | Create a test service and testimonial |
| 3 | Content from Sanity | Edit text in Studio, refresh page, see changes |
| 3 | Images from Sanity | Upload image in Studio, see it on site |
| 3 | ISR works | Edit content, wait 60s, refresh, see updates |
| 4 | Testimonials render | Check home page testimonial section |
| 4 | Calendly loads | Visit /booking, verify widget renders and is interactive |
| 5 | Form validation | Submit empty form, verify error messages |
| 5 | Form submission | Fill form, submit, check email arrives |
| 5 | Form error state | Test with invalid email |
| 6 | SEO meta | View page source, check title/description/OG tags |
| 6 | Lighthouse | Run audit, verify > 90 on Performance, Accessibility, SEO |
| 6 | Mobile | Test on phone or Chrome DevTools mobile emulation |
| 6 | Studio on production | Log into production /studio, edit content, verify it updates |

No automated tests — this is a static marketing site. If the project grows (blog, auth), add Playwright E2E tests then.

## 10. Security Implications

- **Contact form:** User-controlled inputs validated server-side. Resend handles email sanitization. Rate limiting via Vercel's built-in serverless function limits.
- **Sanity API:** Read-only public client (useCdn: true). Write access requires Sanity authentication (Google/GitHub SSO). Dataset set to "public" for reads.
- **Environment variables:** Sanity project ID and dataset are safe to expose client-side (NEXT_PUBLIC_). RESEND_API_KEY stays server-side only (no NEXT_PUBLIC_ prefix).
- **Sanity Studio at /studio:** Protected by Sanity's own auth. No additional auth layer needed.
- **No injection risk:** Next.js App Router uses React Server Components. No dangerouslySetInnerHTML. Content from Sanity rendered as React children.

## 11. Risks & Tradeoffs

| Risk | Mitigation |
|---|---|
| Sennen finds Sanity Studio confusing | Keep schemas simple, add field descriptions. Record a 5-min Loom walkthrough. |
| Unsplash placeholders look generic | Pick carefully (Bali, meditation, wellness). Sennen replaces with real photos ASAP. |
| Calendly free tier = 1 event type | Fine for starting out. Upgrade to TidyCal ($29 lifetime) if she needs multiple. |
| Vercel free tier limits | Coaching site won't hit them. Monitor dashboard. Pro is $20/mo if needed. |
| Sanity content lake = vendor dependency | Acceptable for this scale. Export possible via API if needed later. |
| Resend free tier emails come from onboarding@resend.dev | Looks slightly unprofessional. Once Sennen has a domain, verify it in Resend for custom from address. |

## 12. Open Questions

~~1. Brand name?~~ → **Sennen Life Coaching**
~~2. Booking tool?~~ → **Calendly (free tier)**
~~3. Contact form email?~~ → **Resend**
~~4. Custom domain?~~ → **Not yet, use Vercel subdomain first**
~~5. Real photos?~~ → **Use Unsplash placeholders for now**
~~6. Social platforms?~~ → **All: Instagram, TikTok, LinkedIn, Facebook, X/Twitter**

**Remaining open question:**
1. **Calendly account:** Does Sennen have a Calendly account set up? We need her booking URL for the embed. If not, she'll need to create one (free) and we'll configure the URL in Sanity.
