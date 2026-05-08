import { defineField, defineType } from 'sanity'

export const home = defineType({
  name: 'home',
  title: 'Home Page',
  type: 'document',
  fields: [
    defineField({
      name: 'heroHeading',
      title: 'Hero Heading',
      type: 'string',
      initialValue: 'Rooted in Grace',
    }),
    defineField({
      name: 'heroSubtitle',
      title: 'Hero Subtitle',
      type: 'text',
      initialValue: 'A sanctuary for spiritual alignment, mindful wellness, and the slow-living philosophy. Breathe deeply, you have arrived.',
    }),
    defineField({
      name: 'heroCtaText',
      title: 'Hero CTA Button Text',
      type: 'string',
      initialValue: 'Begin Your Journey',
    }),
    defineField({
      name: 'heroImage',
      title: 'Hero Image',
      type: 'image',
      options: { hotspot: true },
    }),
    defineField({
      name: 'philosophyHeading',
      title: 'Philosophy Section Heading',
      type: 'string',
      initialValue: 'The Art of Slowing Down.',
    }),
    defineField({
      name: 'philosophyBody',
      title: 'Philosophy Body Text',
      type: 'array',
      of: [{ type: 'block' }],
    }),
    defineField({
      name: 'philosophyImage',
      title: 'Philosophy Section Image',
      type: 'image',
      options: { hotspot: true },
    }),
  ],
  preview: {
    prepare() {
      return { title: 'Home Page Content' }
    },
  },
})