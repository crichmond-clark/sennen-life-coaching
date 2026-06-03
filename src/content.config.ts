import { glob } from 'astro/loaders'
import { defineCollection } from 'astro:content'
import { z } from 'zod'

const ctaSchema = z.object({
  text: z.string().min(1),
  href: z.string().min(1),
})

const heroSchema = z.object({
  eyebrow: z.string().optional(),
  heading: z.string().min(1),
  subtitle: z.string().min(1),
  primaryCta: ctaSchema.optional(),
  secondaryCta: ctaSchema.optional(),
  image: z.string().optional(),
  imageAlt: z.string().optional(),
})

const pages = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/pages' }),
  schema: z.object({
    title: z.string().min(1),
    seoTitle: z.string().min(1),
    seoDescription: z.string().min(1),
    hero: heroSchema,
    sections: z.record(z.string(), z.unknown()).optional(),
  }),
})

const services = defineCollection({
  loader: glob({ pattern: '**/*.{yaml,yml}', base: './src/content/services' }),
  schema: z.object({
    title: z.string().min(1),
    slug: z.string().min(1),
    summary: z.string().min(1),
    duration: z.string().min(1),
    price: z.string().default(''),
    format: z.string().min(1),
    sortOrder: z.number().int(),
    features: z.array(z.string().min(1)).default([]),
    ctaText: z.string().min(1).default('Enquire now'),
    ctaHref: z.string().min(1).default('/booking'),
  }),
})

const testimonials = defineCollection({
  loader: glob({ pattern: '**/*.{yaml,yml}', base: './src/content/testimonials' }),
  schema: z.object({
    quote: z.string().min(1),
    authorName: z.string().min(1),
    authorTitle: z.string().optional(),
    relationship: z.string().optional(),
    sortOrder: z.number().int().default(0),
  }),
})

const settings = defineCollection({
  loader: glob({ pattern: '**/*.{yaml,yml}', base: './src/content/settings' }),
  schema: z.object({
    brandName: z.string().min(1),
    tagline: z.string().min(1),
    siteUrl: z.url(),
    contactEmail: z.email(),
    bookingUrl: z.string().default(''),
    formAction: z.string().default(''),
    web3FormsAccessKey: z.string().default(''),
    socialLinks: z
      .array(
        z.object({
          platform: z.enum(['Instagram', 'TikTok', 'LinkedIn', 'Facebook', 'X']),
          url: z.string().default(''),
        }),
      )
      .default([]),
  }),
})

const faqs = defineCollection({
  loader: glob({ pattern: '**/*.{yaml,yml}', base: './src/content/faqs' }),
  schema: z.object({
    question: z.string().min(1),
    answer: z.string().min(1),
    page: z.string().min(1).default('booking'),
    sortOrder: z.number().int().default(0),
  }),
})

export const collections = { pages, services, testimonials, settings, faqs }
