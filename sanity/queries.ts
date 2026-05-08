// GROQ queries for fetching content from Sanity

export const siteSettingsQuery = `*[_type == "siteSettings"][0]{
  brandName,
  tagline,
  logo {
    asset->{
      url,
      metadata
    },
    hotspot
  },
  bookingUrl,
  contactEmail,
  socialLinks[] {
    platform,
    url
  }
}`

export const homeQuery = `{
  "home": *[_type == "home"][0] {
    heroHeading,
    heroSubtitle,
    heroCtaText,
    heroImage {
      asset->{
        url,
        metadata { lqip, dimensions }
      },
      hotspot,
      crop
    },
    philosophyHeading,
    philosophyBody,
    philosophyImage {
      asset->{
        url,
        metadata { lqip, dimensions }
      },
      hotspot,
      crop
    }
  },
  "testimonials": *[_type == "testimonial"] | order(_createdAt desc) {
    quote,
    authorName,
    authorTitle,
    avatar {
      asset->{
        url
      }
    }
  }
}`

export const aboutQuery = `*[_type == "about"][0] {
  heroHeading,
  heroSubtitle,
  heroImage {
    asset->{
      url,
      metadata { lqip, dimensions }
    },
    hotspot,
    crop
  },
  journeySections[] {
    heading,
    body,
    image {
      asset->{
        url,
        metadata { lqip, dimensions }
      },
      hotspot,
      crop
    }
  },
  philosophyCards[] {
    title,
    body,
    iconName
  }
}`

export const servicesQuery = `*[_type == "service"] | order(sortOrder asc) {
  _id,
  title,
  slug {
    current
  },
  price,
  duration,
  description,
  features
}`