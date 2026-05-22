import type { Metadata } from 'next'

export const SITE_URL = 'https://sennenlifecoaching.com'
export const DEFAULT_BRAND_NAME = 'Sennen Life Coaching'
export const DEFAULT_TAGLINE = 'Rooted in Grace'
export const DEFAULT_DESCRIPTION =
  'A sanctuary for spiritual alignment, mindful wellness, and the slow-living philosophy. Breathe deeply, you have arrived.'
export const DEFAULT_OG_IMAGE = '/opengraph-image.jpg'

export const routes = {
  home: '/',
  about: '/about',
  services: '/services',
  testimonials: '/testimonials',
  booking: '/booking',
} as const

export function canonicalUrl(path = '/'): string {
  const normalizedPath = path.startsWith('/') ? path : `/${path}`
  return new URL(normalizedPath, SITE_URL).toString()
}

type PageMetadataOptions = {
  title?: string
  description?: string
  path?: string
  brandName?: string
  tagline?: string
}

export function buildPageMetadata({
  title,
  description = DEFAULT_DESCRIPTION,
  path = '/',
  brandName = DEFAULT_BRAND_NAME,
  tagline = DEFAULT_TAGLINE,
}: PageMetadataOptions = {}): Metadata {
  const url = canonicalUrl(path)
  const fullTitle = title ? `${title} | ${brandName}` : `${brandName} — ${tagline}`

  return {
    ...(title ? { title } : {}),
    description,
    alternates: {
      canonical: url,
    },
    openGraph: {
      type: 'website',
      locale: 'en_US',
      url,
      siteName: brandName,
      title: fullTitle,
      description,
      images: [
        {
          url: DEFAULT_OG_IMAGE,
          width: 1200,
          height: 630,
          alt: `${brandName} — ${tagline}`,
        },
      ],
    },
    twitter: {
      card: 'summary_large_image',
      title: fullTitle,
      description,
      images: [DEFAULT_OG_IMAGE],
    },
  }
}

export function organizationJsonLd(brandName = DEFAULT_BRAND_NAME, tagline = DEFAULT_TAGLINE) {
  return {
    '@context': 'https://schema.org',
    '@type': 'ProfessionalService',
    '@id': `${SITE_URL}/#organization`,
    name: brandName,
    url: SITE_URL,
    slogan: tagline,
    description: DEFAULT_DESCRIPTION,
    areaServed: ['Ubud', 'Online'],
    serviceType: ['Life coaching', 'Spiritual coaching', 'Mindful wellness coaching'],
  }
}

export function websiteJsonLd(brandName = DEFAULT_BRAND_NAME) {
  return {
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    '@id': `${SITE_URL}/#website`,
    name: brandName,
    url: SITE_URL,
    publisher: {
      '@id': `${SITE_URL}/#organization`,
    },
  }
}

export function faqPageJsonLd(faqs: Array<{ question: string; answer: string }>) {
  return {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: faqs.map((faq) => ({
      '@type': 'Question',
      name: faq.question,
      acceptedAnswer: {
        '@type': 'Answer',
        text: faq.answer,
      },
    })),
  }
}
