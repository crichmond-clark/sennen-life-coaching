import { getCollection } from 'astro:content'

export async function getSiteSettings() {
  const entries = await getCollection('settings')
  const site = entries.find((entry) => entry.id === 'site') ?? entries[0]

  if (!site) {
    throw new Error('Missing src/content/settings/site.yaml')
  }

  return site.data
}

export async function getPage(id: string) {
  const pages = await getCollection('pages')
  const page = pages.find((entry) => entry.id === id)

  if (!page) {
    throw new Error(`Missing src/content/pages/${id}.md`)
  }

  return page
}

export async function getServices() {
  const services = await getCollection('services')
  return services.toSorted((a, b) => a.data.sortOrder - b.data.sortOrder)
}

export async function getTestimonials() {
  const testimonials = await getCollection('testimonials')
  return testimonials.toSorted((a, b) => a.data.sortOrder - b.data.sortOrder)
}

export async function getFaqs(page: string) {
  const faqs = await getCollection('faqs')
  return faqs
    .filter((entry) => entry.data.page === page)
    .toSorted((a, b) => a.data.sortOrder - b.data.sortOrder)
}

export function hasExternalUrl(value: string | undefined) {
  return Boolean(value && /^https?:\/\//.test(value))
}
