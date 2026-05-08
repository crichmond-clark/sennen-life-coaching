import { defineField, defineType } from 'sanity'

export const siteSettings = defineType({
  name: 'siteSettings',
  title: 'Site Settings',
  type: 'document',
  fields: [
    defineField({
      name: 'brandName',
      title: 'Brand Name',
      type: 'string',
      initialValue: 'Sennen Life Coaching',
    }),
    defineField({
      name: 'tagline',
      title: 'Tagline',
      type: 'string',
      initialValue: 'Rooted in Grace',
    }),
    defineField({
      name: 'logo',
      title: 'Logo',
      type: 'image',
      options: { hotspot: true },
    }),
    defineField({
      name: 'bookingUrl',
      title: 'Calendly Booking URL',
      type: 'url',
      description: 'Your Calendly scheduling link (e.g., https://calendly.com/your-name)',
    }),
    defineField({
      name: 'contactEmail',
      title: 'Contact Email',
      type: 'string',
      description: 'Where contact form submissions are sent',
    }),
    defineField({
      name: 'socialLinks',
      title: 'Social Links',
      type: 'array',
      of: [
        {
          type: 'object',
          fields: [
            {
              name: 'platform',
              title: 'Platform',
              type: 'string',
              options: {
                list: [
                  { title: 'Instagram', value: 'Instagram' },
                  { title: 'TikTok', value: 'TikTok' },
                  { title: 'LinkedIn', value: 'LinkedIn' },
                  { title: 'Facebook', value: 'Facebook' },
                  { title: 'X', value: 'X' },
                ],
              },
            },
            {
              name: 'url',
              title: 'URL',
              type: 'url',
            },
          ],
        },
      ],
    }),
  ],
  preview: {
    prepare() {
      return {
        title: 'Site Settings',
      }
    },
  },
})