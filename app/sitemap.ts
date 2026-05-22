import { MetadataRoute } from 'next'
import { canonicalUrl, routes } from '@/lib/seo'

export default function sitemap(): MetadataRoute.Sitemap {
  const lastModified = new Date()

  return [
    {
      url: canonicalUrl(routes.home),
      lastModified,
      changeFrequency: 'monthly',
      priority: 1,
    },
    {
      url: canonicalUrl(routes.about),
      lastModified,
      changeFrequency: 'monthly',
      priority: 0.8,
    },
    {
      url: canonicalUrl(routes.services),
      lastModified,
      changeFrequency: 'monthly',
      priority: 0.8,
    },
    {
      url: canonicalUrl(routes.testimonials),
      lastModified,
      changeFrequency: 'monthly',
      priority: 0.7,
    },
    {
      url: canonicalUrl(routes.booking),
      lastModified,
      changeFrequency: 'monthly',
      priority: 0.9,
    },
  ]
}