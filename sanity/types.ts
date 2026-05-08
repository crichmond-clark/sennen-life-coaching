// Site Settings
export interface SocialLink {
  platform: string
  url: string
}

export interface SiteSettings {
  _id: string
  brandName: string
  tagline: string
  logo?: SanityImage
  bookingUrl?: string
  contactEmail?: string
  socialLinks?: SocialLink[]
}

// Home Page
export interface HomePage {
  _id: string
  heroHeading: string
  heroSubtitle: string
  heroCtaText: string
  heroImage?: SanityImage
  philosophyHeading: string
  philosophyBody?: any[]
  philosophyImage?: SanityImage
}

// About Page
export interface JourneySection {
  heading: string
  body: string
  image?: SanityImage
}

export interface PhilosophyCard {
  title: string
  body: string
  iconName: string
}

export interface AboutPage {
  _id: string
  heroHeading: string
  heroSubtitle: string
  heroImage?: SanityImage
  journeySections?: JourneySection[]
  philosophyCards?: PhilosophyCard[]
}

// Service
export interface Service {
  _id: string
  title: string
  slug: { current: string }
  price: string
  duration: string
  description: string
  features?: string[]
  sortOrder: number
}

// Testimonial
export interface Testimonial {
  _id: string
  quote: string
  authorName: string
  authorTitle: string
  avatar?: SanityImage
}

// Image type
export interface SanityImage {
  _type: 'image'
  asset: {
    _ref: string
    _type: 'reference'
    url?: string
  }
  hotspot?: {
    x: number
    y: number
  }
  crop?: {
    top: number
    bottom: number
    left: number
    right: number
  }
}