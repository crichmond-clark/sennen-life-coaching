import { defineField, defineType } from 'sanity'

export const booking = defineType({
  name: 'booking',
  title: 'Booking Page',
  type: 'document',
  fields: [
    defineField({
      name: 'heroHeading',
      title: 'Hero Heading',
      type: 'string',
      initialValue: 'Begin Your Journey',
    }),
    defineField({
      name: 'heroSubtitle',
      title: 'Hero Subtitle',
      type: 'text',
      initialValue: 'Take a deep breath. Inquire about a session below, or simply send a note to connect. I look forward to holding space for you.',
    }),
    defineField({
      name: 'scheduleHeading',
      title: 'Schedule Section Heading',
      type: 'string',
      initialValue: 'Schedule Your Session',
    }),
    defineField({
      name: 'contactHeading',
      title: 'Contact Form Heading',
      type: 'string',
      initialValue: 'Or send a gentle note',
    }),
    defineField({
      name: 'faqHeading',
      title: 'FAQ Heading',
      type: 'string',
      initialValue: 'Good to know',
    }),
    defineField({
      name: 'faqs',
      title: 'FAQs',
      type: 'array',
      of: [
        {
          type: 'object',
          fields: [
            {
              name: 'question',
              title: 'Question',
              type: 'string',
            },
            {
              name: 'answer',
              title: 'Answer',
              type: 'text',
            },
          ],
        },
      ],
    }),
  ],
  preview: {
    prepare() {
      return { title: 'Booking Page Content' }
    },
  },
})
