import { defineField, defineType } from 'sanity'

export const testimonial = defineType({
  name: 'testimonial',
  title: 'Testimonial',
  type: 'document',
  fields: [
    defineField({
      name: 'quote',
      title: 'Quote',
      type: 'text',
    }),
    defineField({
      name: 'authorName',
      title: 'Author Name',
      type: 'string',
    }),
    defineField({
      name: 'authorTitle',
      title: 'Author Title',
      type: 'string',
      description: 'e.g., "Client since 2024"',
    }),
    defineField({
      name: 'avatar',
      title: 'Avatar (optional)',
      type: 'image',
      options: { hotspot: true },
    }),
  ],
  preview: {
    select: {
      title: 'authorName',
      subtitle: 'quote',
    },
  },
})