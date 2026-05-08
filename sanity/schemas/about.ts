import { defineField, defineType } from 'sanity'

export const about = defineType({
  name: 'about',
  title: 'About Page',
  type: 'document',
  fields: [
    defineField({
      name: 'heroHeading',
      title: 'Hero Heading',
      type: 'string',
      initialValue: 'Rooted in Grace, Wandering in Spirit',
    }),
    defineField({
      name: 'heroSubtitle',
      title: 'Hero Subtitle',
      type: 'text',
      initialValue: 'My journey began not with a destination, but with a profound desire to listen. Between the ancient temples of Thailand and the healing waters of Bali, I found the quiet voice that guides my practice today.',
    }),
    defineField({
      name: 'heroImage',
      title: 'Hero Image',
      type: 'image',
      options: { hotspot: true },
    }),
    defineField({
      name: 'journeySections',
      title: 'Journey Sections',
      type: 'array',
      of: [
        {
          type: 'object',
          fields: [
            {
              name: 'heading',
              title: 'Section Heading',
              type: 'string',
            },
            {
              name: 'body',
              title: 'Body Text',
              type: 'text',
            },
            {
              name: 'image',
              title: 'Image',
              type: 'image',
              options: { hotspot: true },
            },
          ],
        },
      ],
    }),
    defineField({
      name: 'philosophyCards',
      title: 'Philosophy Cards',
      type: 'array',
      of: [
        {
          type: 'object',
          fields: [
            {
              name: 'title',
              title: 'Card Title',
              type: 'string',
            },
            {
              name: 'body',
              title: 'Card Body',
              type: 'text',
            },
            {
              name: 'iconName',
              title: 'Icon Name',
              type: 'string',
              options: {
                list: [
                  { title: 'Flower', value: 'Flower' },
                  { title: 'Droplet', value: 'Droplet' },
                  { title: 'Leaf', value: 'Leaf' },
                  { title: 'Sun', value: 'Sun' },
                  { title: 'Heart', value: 'Heart' },
                ],
              },
            },
          ],
        },
      ],
    }),
  ],
  preview: {
    prepare() {
      return { title: 'About Page Content' }
    },
  },
})